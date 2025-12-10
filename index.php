<?php
if (isset($_SESSION['username'])) {
	session_destroy();
	echo '<script>window.location="login/"</script>';
}
else {
	echo '<script>window.location="login/"</script>';
}

?>