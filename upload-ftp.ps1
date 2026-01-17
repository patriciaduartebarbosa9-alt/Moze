# Script de Upload FTP para Infinityfree
# Configura as credenciais
$ftpServer = "ftp.infinityfree.com"
$ftpUser = "if0_40439565"
$ftpPass = "Mozept123"
$ftpPath = "/public_html/"

# Directórios locais a fazer upload
$localPublicPath = "C:\xampp\htdocs\Moze\public"
$localPagesPath = "C:\xampp\htdocs\Moze\pages"

# Função para fazer upload via FTP
function Upload-FTPFile {
    param(
        [string]$localFile,
        [string]$remotePath
    )
    
    if (-Not (Test-Path $localFile)) {
        Write-Host "❌ Ficheiro não encontrado: $localFile" -ForegroundColor Red
        return
    }
    
    try {
        $fileName = Split-Path $localFile -Leaf
        $ftpURI = "ftp://${ftpServer}${remotePath}${fileName}"
        
        Write-Host "⬆️  Upload: $fileName → $remotePath" -ForegroundColor Yellow
        
        # Criar credenciais FTP
        $credential = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        
        # Criar request FTP
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpURI)
        $ftpRequest.Credentials = $credential
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        
        # Ler o ficheiro
        $fileStream = [System.IO.File]::OpenRead($localFile)
        $ftpStream = $ftpRequest.GetRequestStream()
        
        # Fazer upload
        $fileStream.CopyTo($ftpStream)
        $fileStream.Close()
        $ftpStream.Close()
        
        $response = $ftpRequest.GetResponse()
        Write-Host "✅ Upload concluído: $fileName" -ForegroundColor Green
        $response.Close()
        
    } catch {
        Write-Host "❌ Erro no upload de $localFile : $_" -ForegroundColor Red
    }
}

# Fazer upload de todos os ficheiros .html de public/
Write-Host "📁 Fazendo upload de ficheiros de public/..." -ForegroundColor Cyan
Get-ChildItem $localPublicPath -Filter "*.html" -Recurse | ForEach-Object {
    Upload-FTPFile $_.FullName $ftpPath
}

# Fazer upload de todos os ficheiros .html de pages/
Write-Host "📁 Fazendo upload de ficheiros de pages/..." -ForegroundColor Cyan
Get-ChildItem $localPagesPath -Filter "*.html" -Recurse | ForEach-Object {
    Upload-FTPFile $_.FullName $ftpPath
}

Write-Host "🎉 Upload concluído!" -ForegroundColor Green
