<#
.SYNOPSIS
  Secure FTP deploy for bagllet theme / preview mu-plugin only.
.DESCRIPTION
  Reads secrets from .env.local. Allowed remote paths only.
  Backs up existing remote files to qa/backups/ before overwrite.
  No DELETE. Supports -DryRun.
.PARAMETER DryRun
  List actions without uploading.
.PARAMETER What
  theme | preview | all
#>
param(
  [switch]$DryRun,
  [ValidateSet('theme','preview','all')]
  [string]$What = 'all'
)

$ErrorActionPreference = 'Stop'
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

function Read-EnvLocal {
  $path = Join-Path $Root '.env.local'
  if (-not (Test-Path $path)) { throw '.env.local missing' }
  $map = @{}
  Get-Content $path -Encoding UTF8 | ForEach-Object {
    $line = $_.Trim()
    if (-not $line -or $line.StartsWith('#')) { return }
    $i = $line.IndexOf('=')
    if ($i -lt 1) { return }
    $k = $line.Substring(0, $i).Trim()
    $v = $line.Substring($i + 1).Trim().Trim('"').Trim("'")
    $map[$k] = $v
  }
  return $map
}

function Assert-AllowedRemote([string]$remotePath, [switch]$AllowParents) {
  $norm = $remotePath.Replace('\','/').TrimStart('/')
  $allowedExact = @(
    '^wp-content/themes/bagllet(/|$)',
    '^wp-content/plugins/bagllet-widgets(/|$)',
    '^wp-content/mu-plugins/bagllet-[^/]+$'
  )
  foreach ($re in $allowedExact) {
    if ($norm -match $re) { return }
  }
  if ($AllowParents) {
    $parents = @(
      'wp-content',
      'wp-content/themes',
      'wp-content/plugins',
      'wp-content/mu-plugins'
    )
    if ($parents -contains $norm) { return }
  }
  throw "Blocked remote path: $remotePath"
}

$envMap = Read-EnvLocal
$hostName = $envMap['FTP_HOST']
$user = $envMap['FTP_USER']
$pass = $envMap['FTP_PASS']
$ftpRoot = ($envMap['FTP_ROOT'] -replace '\\','/').TrimEnd('/')
if (-not $hostName -or -not $user -or -not $pass) { throw 'FTP_HOST/USER/PASS required in .env.local' }

$cred = New-Object System.Net.NetworkCredential($user, $pass)
$backupRoot = Join-Path $Root 'qa/backups'
New-Item -ItemType Directory -Force -Path $backupRoot | Out-Null

function Get-FtpUri([string]$remoteRel, [switch]$AllowParents) {
  $rel = ($remoteRel -replace '\\','/').TrimStart('/')
  Assert-AllowedRemote $rel -AllowParents:$AllowParents
  $base = if ($ftpRoot) { "$ftpRoot/$rel" } else { $rel }
  return "ftp://$hostName/$base"
}

function Ensure-FtpDir([string]$remoteDirRel) {
  $parts = ($remoteDirRel -replace '\\','/').Trim('/').Split('/')
  $acc = ''
  foreach ($p in $parts) {
    $acc = if ($acc) { "$acc/$p" } else { $p }
    $uri = Get-FtpUri $acc -AllowParents
    try {
      $req = [System.Net.FtpWebRequest]::Create($uri)
      $req.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
      $req.Credentials = $cred
      $req.Proxy = $null
      $req.UsePassive = $true
      $req.EnableSsl = $false
      $resp = $req.GetResponse(); $resp.Close()
    } catch {
      # exists or parent missing — ignore for DryRun/idempotent mkdir
    }
  }
}

function Backup-RemoteFile([string]$remoteRel, [string]$localTmp) {
  $uri = Get-FtpUri $remoteRel
  try {
    $req = [System.Net.FtpWebRequest]::Create($uri)
    $req.Method = [System.Net.WebRequestMethods+Ftp]::DownloadFile
    $req.Credentials = $cred
    $req.Proxy = $null
    $req.UsePassive = $true
    $resp = $req.GetResponse()
    $stream = $resp.GetResponseStream()
    $fs = [System.IO.File]::Create($localTmp)
    $stream.CopyTo($fs)
    $fs.Close(); $stream.Close(); $resp.Close()
    return $true
  } catch {
    return $false
  }
}

function Upload-File([string]$localPath, [string]$remoteRel) {
  Assert-AllowedRemote $remoteRel
  $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
  $bakName = ($remoteRel -replace '[\\/]','_') + '.' + $stamp + '.bak'
  $bakPath = Join-Path $backupRoot $bakName
  if (-not $DryRun) {
    $null = Backup-RemoteFile $remoteRel $bakPath
  }
  Write-Host ("{0} {1} -> {2}" -f ($(if ($DryRun) { 'DRY' } else { 'PUT' }), $localPath, $remoteRel))
  if ($DryRun) { return }
  $uri = Get-FtpUri $remoteRel
  $req = [System.Net.FtpWebRequest]::Create($uri)
  $req.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
  $req.Credentials = $cred
  $req.Proxy = $null
  $req.UsePassive = $true
  $req.UseBinary = $true
  $bytes = [System.IO.File]::ReadAllBytes($localPath)
  $req.ContentLength = $bytes.Length
  $stream = $req.GetRequestStream()
  $stream.Write($bytes, 0, $bytes.Length)
  $stream.Close()
  $resp = $req.GetResponse(); $resp.Close()
}

function Publish-Tree([string]$localDir, [string]$remotePrefix) {
  Get-ChildItem -Path $localDir -Recurse -File | ForEach-Object {
    $rel = $_.FullName.Substring((Resolve-Path $localDir).Path.Length).TrimStart('\','/')
    $remoteRel = ($remotePrefix.TrimEnd('/') + '/' + ($rel -replace '\\','/')).TrimStart('/')
    $remoteDir = Split-Path $remoteRel -Parent
    if ($remoteDir) {
      if (-not $DryRun) { Ensure-FtpDir ($remoteDir -replace '\\','/') }
    }
    Upload-File $_.FullName $remoteRel
  }
}

# Generate preview key file locally (never commit)
$keyVal = $envMap['BAGLLET_PREVIEW_KEY']
$keyLocal = Join-Path $Root 'public_html/wp-content/mu-plugins/bagllet-preview-key.php'
if ($keyVal) {
  $escaped = $keyVal.Replace("'", "\'")
  $content = @"
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'BAGLLET_PREVIEW_KEY', '$escaped' );
"@
  if (-not $DryRun) {
    [System.IO.File]::WriteAllText($keyLocal, $content, [System.Text.UTF8Encoding]::new($false))
  }
  Write-Host "preview key file ready (local, gitignored if matched)"
}

if ($What -eq 'theme' -or $What -eq 'all') {
  Publish-Tree (Join-Path $Root 'public_html/wp-content/themes/bagllet') 'wp-content/themes/bagllet'
}
if ($What -eq 'preview' -or $What -eq 'all') {
  Upload-File (Join-Path $Root 'public_html/wp-content/mu-plugins/bagllet-preview.php') 'wp-content/mu-plugins/bagllet-preview.php'
  if (Test-Path $keyLocal) {
    Upload-File $keyLocal 'wp-content/mu-plugins/bagllet-preview-key.php'
  }
}

Write-Host 'Deploy finished.'
