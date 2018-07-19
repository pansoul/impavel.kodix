insert into `kdx_brand` (`ID`, `NAME`) values('1','Ford (+)');
insert into `kdx_brand` (`ID`, `NAME`) values('2','Acura');
insert into `kdx_brand` (`ID`, `NAME`) values('3','Chevrolet');
insert into `kdx_brand` (`ID`, `NAME`) values('4','Honda');
insert into `kdx_brand` (`ID`, `NAME`) values('5','Kia');
insert into `kdx_brand` (`ID`, `NAME`) values('6','Nissan');
insert into `kdx_brand` (`ID`, `NAME`) values('7','Toyota (+)');

insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('1','Модель 1 для Focus (+)','1');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('2','Модель 2 для Focus (+)','1');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('3','Модель 3 для Focus','1');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('4','Модель 4 для Focus','1');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('5','Модель 5 для Focus','1');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('6','Модель 1 для Toyota (+)','7');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('7','Модель 2 для Toyota','7');
insert into `kdx_model` (`ID`, `NAME`, `BRAND_ID`) values('8','Модель 3 для Toyota','7');

insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('1','Комплектация 1 для Модели 1 Focus (+)','1');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('2','Комплектация 2 для Модели 1 Focus','1');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('3','Комплектация 3 для Модели 1 Focus','1');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('4','Комплектация 1 для Модели 2 Focus','2');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('5','Комплектация 2 для Модели 2 Focus','2');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('6','Комплектация 3 для Модели 2 Focus','2');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('7','Комплектация 1 для Модели 6 Toyota (+)','6');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('8','Комплектация 2 для Модели 6 Toyota (+)','6');
insert into `kdx_compl` (`ID`, `NAME`, `MODEL_ID`) values('9','Комплектация 3 для Модели 6 Toyota','6');

insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('1','Авто 1 для Комплектации 1 и Модели 1 Focus','2007','300000','1');
insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('2','Авто 2 для Комплектации 1 и Модели 1 Focus','2008','350000','1');
insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('3','Авто 3 для Комплектации 1 и Модели 1 Focus','2015','500000','1');
insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('4','Авто 1 для Комплектации 7 и Модели 6 Toyota','2005','280000','7');
insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('5','Авто 2 для Комплектации 7 и Модели 6 Toyota','2005','300000','7');
insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('6','Авто 3 для Комплектации 7 и Модели 6 Toyota','2013','450000','7');
insert into `kdx_car` (`ID`, `NAME`, `YEAR`, `PRICE`, `COMPL_ID`) values('7','Авто 4 для Комплектации 7 и Модели 6 Toyota','2005','310000','8');

insert into `kdx_option` (`ID`, `NAME`) values('1','магнитола');
insert into `kdx_option` (`ID`, `NAME`) values('2','сигнализация');
insert into `kdx_option` (`ID`, `NAME`) values('3','коврики');
insert into `kdx_option` (`ID`, `NAME`) values('4','17-дюймовые диски');

insert into `kdx_compl_option` (`ID`, `OPTION_ID`, `COMPL_ID`) values('1','1','1');
insert into `kdx_compl_option` (`ID`, `OPTION_ID`, `COMPL_ID`) values('2','2','1');
insert into `kdx_compl_option` (`ID`, `OPTION_ID`, `COMPL_ID`) values('3','1','2');
insert into `kdx_compl_option` (`ID`, `OPTION_ID`, `COMPL_ID`) values('4','2','2');
insert into `kdx_compl_option` (`ID`, `OPTION_ID`, `COMPL_ID`) values('5','4','1');

insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('1','4','1');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('2','3','1');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('3','3','2');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('4','3','3');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('5','3','4');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('6','3','5');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('7','3','6');
insert into `kdx_car_option` (`ID`, `OPTION_ID`, `CAR_ID`) values('8','3','7');