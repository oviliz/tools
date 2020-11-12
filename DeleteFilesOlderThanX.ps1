### Task Scheduler > Start a program
### powershell
### -Command "& C:\PathToThisScript\DeleteFilesOlderThanX.ps1 -Directory D:\PathDo\FilesToBeDeleted -DaysToKeep 7"

PARAM (
    [parameter(Mandatory=$true)]
    [string]$Directory,

    [parameter(Mandatory=$true)]
    [int]$DaysToKeep
)

$ThresholdDate = (Get-Date).AddDays(-$DaysToKeep)

Get-ChildItem -Path $Directory | Where-Object {$_.LastWriteTime -lt $ThresholdDate} | Remove-Item -Force
