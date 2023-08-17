<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                   @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_admin_create_backup() {
    global $mysqli;

    require_once(cl_full_path("core/libs/MySQL-Dump/MySQLDump.php"));
    
    $time   = time();
    $date   = date('d-m-Y');
    $backup = new MySQLDump($mysqli);

    try {
        if (file_exists(cl_full_path(cl_strf("site_backups/%s_%s",$date,$time))) != true) {
            mkdir(cl_full_path(cl_strf("site_backups/%s_%s",$date,$time)), 0777, true);
        }

        if (file_exists(cl_full_path(cl_strf("site_backups/%s_%s/index.html",$date,$time))) != true) {
            file_put_contents(cl_full_path(cl_strf("site_backups/%s_%s/index.html",$date,$time)), "0");
        }

        if (file_exists(cl_full_path('site_backups/.htaccess')) != true) {
            file_put_contents(cl_full_path('site_backups/.htaccess'), "deny from all\nOptions -Indexes");
        }

        if (file_exists(cl_full_path('site_backups/index.html')) != true) {
            file_put_contents(cl_full_path("site_backups/index.html"), "0");
        }

        $folder_name  = cl_strf("site_backups/%s_%s", $date, $time);
        $sql_backup   = cl_strf("%s/db-backup-%s-%s.sql",$folder_name, $date, $time);
        $files_backup = cl_strf("%s/script-files-backup-%s-%s.zip",$folder_name, $date, $time);
        $put          = $backup->save($sql_backup);
        $root_path    = PROJ_RP;
        $zip          = new ZipArchive();
        $act          = (ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $open         = $zip->open($files_backup, $act);

        if ($open !== true) {
            return false;
        }

        $riiter = RecursiveIteratorIterator::LEAVES_ONLY;
        $rditer = new RecursiveDirectoryIterator($root_path);
        $files  = new RecursiveIteratorIterator($rditer,$riiter);

        foreach ($files as $name => $file) {
            if (preg_match('/\bsite_backups\b/', $file) != true) {
                if ($file->isDir() != true) {
                    $file_path     = $file->getRealPath();
                    $relative_path = mb_substr($file_path, (len($root_path) + 1));
                    $zip->addFile($file_path, $relative_path);
                }
            }
        }

        $zip->close();

        return true;    
    } 

    catch (Exception $e) {
        return false;
    }
}