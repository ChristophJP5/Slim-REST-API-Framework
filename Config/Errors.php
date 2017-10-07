<?php

namespace API\Config;

class errors {

    public static $emptyResponse   = "Keine daten vorhanden";
    public static $emptyDBSettings = "Fehlende DB einstellungen";
    public static $missingMethod   = "Methode  existiert nicht";
    public static $missingEndpoint = "Endpoint existiert nicht";
    public static $invalidSession  = "Session ist nicht valide";
    public static $invalidId       = "Id in Endpoint does not exists";
    public static $missingParams   = "Parameter nicht gesetzt";
}
