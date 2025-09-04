# plugin/weaver/tools/flatten.ps1

param(
  [Parameter(Mandatory=$true)][string]$Root,
  [Parameter(Mandatory=$true)][string]$Out,
  [string]$Sel = "",
  [switch]$All,
  [switch]$WvDebug
)

$ErrorActionPreference = "Stop"
$SEP = "__"

# 콘솔 출력 UTF-8 시도 (가능한 경우)
try { [Console]::OutputEncoding = [Text.UTF8Encoding]::new($false) } catch {}

function To-FlatName([string]$root, [string]$full){
    $rel = $full.Substring($root.Length).TrimStart('\','/')
    $rel = $rel -replace '[:*?"<>|]', '_'  # 금지문자 치환
    $rel = $rel -replace '[\\/]+', $SEP    # 경로구분자 -> "__"
    return $rel
}

# PhpStorm 매크로 리터럴이 그대로 들어온 경우 무시
$macroLiterals = @('$SelectedPaths$', '$SelectedFiles$')
if ($macroLiterals -contains $Sel) { $Sel = "" }

# 대상 수집
$INCLUDE = @()

if ($All) {
    $INCLUDE = Get-ChildItem -Path $Root -Recurse -File | ForEach-Object { $_.FullName }
} else {
    if ($Sel -ne "") {
        # 경로 구분자: | ; 줄바꿈 모두 대응
        $split = $Sel -split '[|;`r`n]+' | ForEach-Object { $_.Trim('"') } | Where-Object { $_ -and (Test-Path $_) }
        foreach ($p in $split) {
            if (Test-Path $p -PathType Leaf) {
                $INCLUDE += $p
            } elseif (Test-Path $p -PathType Container) {
                $INCLUDE += (Get-ChildItem -Path $p -Recurse -File | ForEach-Object { $_.FullName })
            }
        }
    }

    # 선택 없음 → 안전 종료
    if ($Sel -eq "" -or $INCLUDE.Count -eq 0) {
        Write-Warning "No selection. Use -All to process entire project or pass -Sel."
        if ($WvDebug) {
            # 디버그 로그만 남김
            New-Item -Force -ItemType Directory -Path $Out | Out-Null
            $dbg = Join-Path $Out "_debug.txt"
            @(
              "PSVersion: $($PSVersionTable.PSVersion)"
              "Root: $Root"
              "Out: $Out"
              "Sel(raw): $Sel"
              "IncludeCount: $($INCLUDE.Count)"
            ) | Out-File -FilePath $dbg -Encoding UTF8
        }
        exit 2
    }
}

# 작업 디렉토리 준비
New-Item -Force -ItemType Directory -Path $Out | Out-Null

# 디버그 로그
if ($WvDebug) {
    $dbg = Join-Path $Out "_debug.txt"
    @(
      "PSVersion: $($PSVersionTable.PSVersion)"
      "Root: $Root"
      "Out: $Out"
      "Sel(raw): $Sel"
      "All: $All"
      "IncludeCount: $($INCLUDE.Count)"
    ) + ($INCLUDE | ForEach-Object { "INC: $_" }) | Out-File -FilePath $dbg -Encoding UTF8
}

# 평탄화 복사 + 매핑 CSV
$used = @{}
$mapCsv = Join-Path $Out "_map.csv"
"original_path,flat_name" | Out-File -FilePath $mapCsv -Encoding UTF8

foreach ($full in $INCLUDE) {
    if (-not ($full -like "$Root*")) { continue }  # 루트 바깥 제외

    $flat = To-FlatName $Root $full
    $dest = Join-Path $Out $flat
    $base = [System.IO.Path]::GetFileNameWithoutExtension($dest)
    $ext  = [System.IO.Path]::GetExtension($dest)
    $key  = $dest.ToLower()

    # 파일명 충돌 방지: (1), (2)...
    $i = 1
    while ($used.ContainsKey($key) -or (Test-Path $dest)) {
        $dest = Join-Path $Out ($base + "($" + $i + ")" + $ext)
        $key  = $dest.ToLower()
        $i++
    }
    $used[$key] = $true

    Copy-Item -LiteralPath $full -Destination $dest

    $line = '"' + ($full.Replace('"','""')) + '","' + ((Split-Path -Leaf $dest).Replace('"','""')) + '"'
    Add-Content -Path $mapCsv -Value $line
}

Write-Host "Flattened: $($INCLUDE.Count) file(s) -> $Out"
