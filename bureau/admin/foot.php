</div> <!-- div content -->
</td>
</tr>
</table>
<?php endBox(); ?>
</div> <!-- div global -->
<?php

$tempsFin = microtimeFloat();
$temps = round($tempsFin - $tempsDebut, 4);

if ($customErrorHandler)
{
	$message = $queryCount . " queries<br />Generated in " . $temps . "s<br />Mem. Usage: " . format_size(memory_get_usage());

	displayPhpError($message);
}

?>
</body>
</html>