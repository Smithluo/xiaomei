@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../talesoft/tale-jade/tale-jade
php "%BIN_TARGET%" %*
