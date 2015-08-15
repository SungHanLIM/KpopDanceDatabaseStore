<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Create $wf_backup Object.
 *
 * @since 4.5.0
 * @uses WF_Backup
 */
global $wf_backup;
$wf_backup = new WF_Backup();
?>