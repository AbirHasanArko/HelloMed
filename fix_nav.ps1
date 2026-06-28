$file = 'd:\Documents\HelloMed\hellomed-laravel\resources\views\layouts\app.blade.php'
$lines = Get-Content $file -Encoding UTF8

Write-Host "Total lines: $($lines.Count)"

$oldNavStart = -1
$mobileOverlayStart = -1

for ($i = 0; $i -lt $lines.Count; $i++) {
    # Find the old nav div (it has the old 26x26 SVG brand logo)
    if ($oldNavStart -eq -1 -and $lines[$i] -match '^\s*<div class="nav">') {
        $oldNavStart = $i
        Write-Host "Found old nav at 0-indexed: $i"
    }
    # Find the mobile nav overlay div (HTML element, not CSS comment)
    if ($lines[$i] -match '<div class="mobile-nav-overlay"') {
        $mobileOverlayStart = $i
        Write-Host "Found mobile overlay at 0-indexed: $i"
    }
}

Write-Host "Old nav start: $oldNavStart, Mobile overlay start: $mobileOverlayStart"

if ($oldNavStart -ge 0 -and $mobileOverlayStart -gt $oldNavStart) {
    # Keep everything before old nav, then skip to mobile overlay comment line (2 lines before the div: the blank line + Blade comment)
    # Find the Blade comment {{-- ===== MOBILE ... before the div
    $insertFrom = $mobileOverlayStart
    for ($j = $mobileOverlayStart - 1; $j -ge ($mobileOverlayStart - 5); $j--) {
        if ($lines[$j] -match 'MOBILE FULL-SCREEN NAV OVERLAY') {
            $insertFrom = $j - 1  # include the blank line before it
            break
        }
    }
    Write-Host "Inserting from line (0-indexed): $insertFrom"
    
    $before = $lines[0..($oldNavStart - 1)]
    $after  = $lines[$insertFrom..($lines.Count - 1)]
    
    $newContent = $before + $after
    [System.IO.File]::WriteAllLines($file, $newContent, [System.Text.UTF8Encoding]::new($false))
    Write-Host "SUCCESS. New total lines: $($newContent.Count)"
} else {
    Write-Host "ERROR: markers not valid. oldNavStart=$oldNavStart mobileOverlayStart=$mobileOverlayStart"
}
