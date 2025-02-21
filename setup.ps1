$dbName
$dbUser
$dbPassword

$mysql = "C:\xampp\mysql\bin\mysql.exe"
$params = "-u ", $dbUser, '-p""'

if(Test-Path .env){
    $envContent = Get-Content .\.env
    foreach($var in $envContent){
        $splitVar = $var.Split('=')
        
        switch ($splitVar[0]) {
            "DB_DATABASE" { 
                $dbName = $splitVar[1]
             }
             "DB_USERNAME" { 
                $dbUser = $splitVar[1]
             }
             "DB_PASSWORD" { 
                $dbPassword = $splitVar[1]
             }
            Default {}
        }

        & $mysql @params -e "SHOW DATABASES"
    }

    Write-Output $dbName
    Write-Output $dbUser
    Write-Output $dbPassword
}
else{
    throw ".env fájl nem létezik"
}