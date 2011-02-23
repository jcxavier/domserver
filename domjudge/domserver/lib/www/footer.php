<?php
/**
 * Common page footer
 * $Id: footer.php 2102 2008-05-31 11:14:57Z eldering $
 */ 
if (!defined('DOMJUDGE_VERSION')) die("DOMJUDGE_VERSION not defined.");

if ( DEBUG & DEBUG_TIMINGS ) {
	echo "<p>"; totaltime(); echo "</p>";
} ?>

</body>
</html>
