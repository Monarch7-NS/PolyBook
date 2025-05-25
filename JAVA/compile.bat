@echo off
echo =====================================
echo     PolyBook - Compilation Java
echo =====================================
echo.

REM Vérification de Java
java -version >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo ❌ Java n'est pas installé ou pas dans le PATH
    echo Installez Java JDK 8+ et ajoutez-le au PATH
    pause
    exit /b 1
)

REM Vérification du driver MySQL
if not exist "lib\mysql-connector-java-8.0.33.jar" (
    echo ❌ Driver MySQL manquant !
    echo.
    echo Téléchargez mysql-connector-java-8.0.33.jar depuis :
    echo https://dev.mysql.com/downloads/connector/j/
    echo.
    echo Placez-le dans le dossier lib/
    pause
    exit /b 1
)

REM Créer le dossier bin s'il n'existe pas
if not exist "bin" (
    echo 📁 Création du dossier bin/
    mkdir bin
)

echo 🔨 Compilation en cours...
echo.

REM Compilation avec le driver MySQL dans le classpath
javac -cp "lib\mysql-connector-java-8.0.33.jar" -d bin src\polybook\*.java src\polybook\model\*.java src\polybook\ui\*.java

if %ERRORLEVEL% == 0 (
    echo ✅ Compilation réussie !
    echo 📂 Les fichiers .class sont dans le dossier bin/
    echo.
    echo Vous pouvez maintenant exécuter run.bat
) else (
    echo ❌ Erreur de compilation !
    echo Vérifiez votre code Java et réessayez
)

echo.
pause
