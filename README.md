# FHC-AddOn-OpenProject
Daten-Schnittstelle von FHC nach OpenProject

## Features
* Anzeige der Daten eines Planner-Projekts (/addons/openproject/vilesci/index.dist.php/planner/FHC_Projekt/projekt/$Projekt$)
* &Uuml;bertragen der Daten nach OpenProject (/addons/openproject/vilesci/index.dist.php/Sync?projekt_kurzbz=$Projekt$)
* Konfigurieren des Work Package (Type, Status) und Role Mappings  (/addons/openproject/vilesci/index.dist.php/Configure)

Wobei $Projekt$ der projekt_kurzbz in der Planner-Datenbank entspricht.

## Systemvoraussetzungen
* Postgresql >= 9.4
* PHP >= 5.4

## Installation
 * Addon in Ordner /addons/openproject/ entpacken
 * Addon im Config aktivieren
