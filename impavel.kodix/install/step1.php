<?
if($ex = $APPLICATION->GetException()):
	echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "ERROR",
		"MESSAGE" => GetMessage("MOD_INST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true,
));
else:
?>

<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANG?>" />
	<input type="hidden" name="id" value="impavel.kodix" />
	<input type="hidden" name="install" value="Y" />
	<input type="hidden" name="step" value="2" />
        
        <?echo CAdminMessage::ShowMessage(GetMessage("kodix_REINSTALL_TABLES_WARN"))?>
	<p><?echo GetMessage("kodix_REINSTALL_TABLES_MESS")?></p>
	<p><input type="checkbox" name="remove_tables" id="remove_tables" value="Y" checked><label for="remove_tables"><?echo GetMessage("kodix_REINSTALL_TABLES_LABEL")?></label></p>
        
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_INSTALL")?>" />
</form>

<?
endif;