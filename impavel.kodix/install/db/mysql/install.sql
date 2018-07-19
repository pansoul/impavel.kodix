CREATE TABLE IF NOT EXISTS `kdx_brand` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `NAME` varchar(255) NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `kdx_car` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `NAME` varchar(255) NOT NULL,
    `YEAR` smallint(4) NOT NULL,
    `PRICE` int(11) NOT NULL,
    `COMPL_ID` int(11) NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `kdx_car_option` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `OPTION_ID` int(11) NOT NULL,
    `CAR_ID` int(11) NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `kdx_compl` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `NAME` varchar(255) NOT NULL,
    `MODEL_ID` int(11) NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `kdx_compl_option` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `OPTION_ID` int(11) NOT NULL,
    `COMPL_ID` int(11) NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `kdx_model` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `NAME` varchar(255) NOT NULL,
    `BRAND_ID` int(11) NOT NULL,
    PRIMARY KEY (`ID`)
);        
        
CREATE TABLE IF NOT EXISTS `kdx_option` (
    `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `NAME` varchar(255) NOT NULL,
    PRIMARY KEY (`ID`)
);
        