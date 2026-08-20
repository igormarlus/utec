# Executado semanalmente pelo Windows Task Scheduler (tarefa "UTec-SEO-GEO-Agent-Weekly").
# Roda o skill seo-geo-agent em modo headless, restrito às ferramentas necessárias.
# Nunca inclui git add/commit/push nem deploy — ver .claude/skills/seo-geo-agent/SKILL.md.

$ErrorActionPreference = "Continue"

$repoPath = "C:\htdocs\utec"
$claudeExe = "C:\Users\Igor_\.local\bin\claude.exe"

$logDir = Join-Path $repoPath "docs\seo-geo-agente-log"
if (-not (Test-Path $logDir)) { New-Item -ItemType Directory -Path $logDir | Out-Null }
$logFile = Join-Path $logDir ("run-{0}.log" -f (Get-Date -Format "yyyy-MM-dd"))

Set-Location $repoPath

$allowedTools = "Read Write Edit Glob Grep Bash(curl:*) Bash(git status:*) Bash(git diff:*) Bash(date:*) Bash(php -l:*) PowerShell(Send-MailMessage:*)"
$disallowedTools = "Bash(git add*) Bash(git commit*) Bash(git push*) Bash(git checkout*) Bash(git reset*) Bash(rm*) Bash(del*)"

"===== $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss') — iniciando execucao semanal =====" | Out-File -FilePath $logFile -Append -Encoding UTF8

& $claudeExe -p "/seo-geo-agent" --allowedTools $allowedTools --disallowedTools $disallowedTools *>> $logFile

"===== $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss') — execucao finalizada (exit code $LASTEXITCODE) =====" | Out-File -FilePath $logFile -Append -Encoding UTF8
