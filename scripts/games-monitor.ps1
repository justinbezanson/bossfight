# Configuration — customize these
$ApiUrl = "http://localhost"
$ApiToken = "YOUR_API_TOKEN"
$KidId = 1

Write-Output "[$(Get-Date)] Monitoring games..."

# Fetch game list
try {
    $Games = Invoke-RestMethod -Uri "$ApiUrl/api/games" `
        -Headers @{ Authorization = "Bearer $ApiToken"; Accept = "application/json" } `
        -ErrorAction Stop
} catch {
    Write-Error "Failed to fetch games: $_"
    exit 1
}

foreach ($Game in $Games) {
    $GameId = $Game.id
    $Name = $Game.name
    $Processes = $Game.processes

    Write-Output "[$Name] Checking processes..."

    $Found = $false
    foreach ($Proc in $Processes) {
        if ($Proc -like "java:*") {
            $SearchTerm = $Proc.Substring(5)
            $JavaProcesses = Get-Process -Name java -ErrorAction SilentlyContinue
            if ($JavaProcesses -and ($JavaProcesses | ForEach-Object { $_.CommandLine } | Select-String -Pattern $SearchTerm -Quiet)) {
                Write-Output "[$Name] Process running: $Proc"
                $Found = $true
            }
        } elseif (Get-Process -Name $Proc -ErrorAction SilentlyContinue) {
            Write-Output "[$Name] Process running: $Proc"
            $Found = $true
        }
    }

    if (-not $Found) {
        Write-Output "[$Name] No processes running"
    } else {
        $Body = @{ kid_id = $KidId; game_id = $GameId; message = "Game running: $Name" } | ConvertTo-Json

        try {
            Invoke-RestMethod -Uri "$ApiUrl/api/log/create" `
                -Method Post `
                -Headers @{ Authorization = "Bearer $ApiToken"; "Content-Type" = "application/json"; Accept = "application/json" } `
                -Body $Body `
                -ErrorAction Stop | Out-Null
            Write-Output "[$Name] Logged"
        } catch {
            Write-Error "[$Name] Failed to log: $_"
        }
    }
}
