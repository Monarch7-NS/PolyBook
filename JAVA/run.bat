@echo off
echo =====================================
echo     PolyBook - Lancement Admin
echo =====================================
echo.

REM Vérifier si les classes sont compilées
if not exist "bin\polybook\Main.class" (
    echo ❌ Classes non compilées !
    echo.
    echo Exécutez d'abord compile.bat pour compiler le projet
    pause
    exit /b 1
)

REM Vérifier le driver MySQL
if not exist "lib\mysql-connector-java-8.0.33.jar" (
    echo ❌ Driver MySQL manquant dans lib/
    pause
    exit /b 1
)

echo 🚀 Lancement de l'interface d'administration...
echo.
echo 📋 Configuration détectée :
echo    • Base de données : polybook
echo    • Serveur MySQL : localhost:3306
echo    • Utilisateur : root
echo.

REM Lancement avec le driver MySQL dans le classpath
java -cp "bin;lib\mysql-connector-java-8.0.33.jar" polybook.Main

echo.
echo 🔚 Application fermée
pause
