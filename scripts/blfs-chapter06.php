#! /usr/bin/php
<?php

include 'blfs-include.php';

$CHAPTER       = '6';
$CHAPTERS      = 'Chapters 6-8';
$START_PACKAGE = 'bluefish';
$STOP_PACKAGE  = 'qemu';

$renames = array();
$renames[ 'zsh1' ] = 'zsh-doc';

$ignores = array();

//$current="dash";   // For debugging

$regex = array();
$regex[ 'joe'  ] = "/^.*Download joe-(\d[\d\.]+).tar.*$/";
$regex[ 'tcsh' ] = "/^.*release is (\d[\d\.]+)\..*$/";

$sf = 'sourceforge.net';

$url_fix = array (

  array( 'pkg'     => 'joe',
         'match'   => '^.*$', 
         'replace' => "http://$sf/projects/joe-editor/files" ),

  array( 'pkg'     => 'emacs',
         'match'   => '^.*$', 
         'replace' => "http://git.savannah.gnu.org/cgit/emacs.git/" ),

  array( 'pkg'     => 'vim',
         'match'   => '^.*$', 
         'replace' => "http://ftp.vim.org/vim/unix" ),

  array( 'pkg'     => 'tcsh',
         'match'   => '^.*$', 
         'replace' => "http://www.tcsh.org/MostRecentRelease" ),

  array( 'pkg'     => 'nano',
         'match'   => '^.*$', 
         'replace' => "https://www.nano-editor.org/dist/" ),

  array( 'pkg'     => 'dash',
         'match'   => '^.*$', 
         'replace' => "https://git.kernel.org/pub/scm/utils/dash/dash.git/refs" ),
);

function get_packages( $package, $dirpath )
{
  global $exceptions;
  global $regex;
  global $book_index;
  global $url_fix;
  global $current;

  if ( isset( $current ) && $book_index != "$current" ) return 0;

  // Fix up directory path
  foreach ( $url_fix as $u )
  {
     $replace = $u[ 'replace' ];
     $match   = $u[ 'match'   ];

     if ( isset( $u[ 'pkg' ] ) )
     {
        if ( $package == $u[ 'pkg' ] )
        {
           $dirpath = preg_replace( "/$match/", "$replace", $dirpath );
           break;
        }
     }
     else
     {
        if ( preg_match( "/$match/", $dirpath ) )
        {
           $dirpath = preg_replace( "/$match/", "$replace", $dirpath );
           break;
        }
     }
  }

  if ( preg_match( "/^ftp/", $dirpath ) ) $dirpath .= "/";
  $lines = http_get_file( "$dirpath" );

  if ( ! is_array( $lines ) ) return $lines;
  
  if ( isset( $regex[ $package ] ) )
  {
     // Custom search for latest package name
     foreach ( $lines as $l )
     {
        $ver = preg_replace( $regex[ $package ], "$1", $l );
        if ( $ver == $l ) continue;
        
        if ( $package == 'krb' ) $ver = "5-$ver";
        return $ver;  // Return first match of regex
     }

     return 0;  // This is an error
  }

  if ( $book_index == "joe" )
  {
    $dir   = find_max( $lines, '/^.*joe-[\d\.]+.*$/', '/^.*(joe-[\d\.]+).tar.*$/' );
    $lines = http_get_file( "$dirpath/$dir" );
  }

  // vim language pack
  if ( $book_index == "vim1" )
    return find_max( $lines, '/^.*vim-[\d\.]+-lang.*$/', '/^.*vim-([\d\.]+)-lang.*$/' );

  if ( $book_index == "nano" )
  {
    $dir = find_max( $lines, "/v/", "/^.*(v[\d\.]+)\/.*$/" );    
    $lines = http_get_file( "$dirpath/$dir" );
  }

  // zsh docs 
  if ( $book_index == "zsh1" )
    return find_max( $lines, '/doc/', '/^.*zsh-([\d\.]+)-doc.tar.*$/' );

  // emacs
  if ( $book_index == "emacs" )
    return find_max( $lines, '/emacs/', '/^.*emacs-([\d\.]+).tar.*$/' );

  if ( $book_index == "dash" )
    return find_max( $lines, '/^v\d/', '/^v([\d\.]+).*$/' );

  // Most packages are in the form $package-n.n.n
  // Occasionally there are dashes (e.g. 201-1)
  $max = find_max( $lines, "/$package/", "/^.*$package-([\d\.]*\d)\.tar.*$/" );
  return $max;
}

Function get_pattern( $line )
{
   // Set up specific patter matches for extracting book versions
   $match = array();

   //$match[ 0 ] = array( 'pkg'   => 'ntfs', 
   //                     'regex' => "/ntfs-3g_ntfsprogs-(\d.*\d)\D*$/" );

   foreach( $match as $m )
   {
      $pkg = $m[ 'pkg' ];
      if ( preg_match( "/$pkg/", $line ) ) 
         return $m[ 'regex' ];
   }

   return "/\D*(\d.*\d)\D*$/";
}

get_current();  // Get what is in the book

// Get latest version for each package 
foreach ( $book as $pkg => $data )
{
   $book_index = $pkg; 

   $base = $data[ 'basename' ];
   $url  = $data[ 'url' ];
   $bver = $data[ 'version' ];

echo "book index: $book_index $bver $url\n";

   $v = get_packages( $base, $url );

   $vers[ $book_index ] = $v;
}

html();  // Write html output
?>
