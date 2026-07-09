# Configuration — customize these
$ApiUrl = "http://localhost"
$ApiToken = "YOUR_API_TOKEN"
$KidId = 1

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
    $Name = $Game.name
    $Processes = $Game.processes

    $Found = $false
    foreach ($Proc in $Processes) {
        if (Get-Process -Name $Proc -ErrorAction SilentlyContinue) {
            Write-Output "[$Name] Process running: $Proc"
            $Found = $true
        }
    }

    if ($Found) {
        $Body = @{ kid_id = $KidId; message = "Game running: $Name" } | ConvertTo-Json

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
