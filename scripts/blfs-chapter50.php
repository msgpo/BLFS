#! /usr/bin/php
<?php

include 'blfs-include.php';

$CHAPTER       = '50';
$CHAPTERS      = 'Chapters 50-55';
$START_PACKAGE = 'cups';
$STOP_PACKAGE  = 'xindy';

$renames = array();
$renames[ 'v' ] = "biber";

$ignores = array();
$ignores[ 'install-tl-unx' ] = '';
$ignores[ 'texlive1'       ] = '';
$ignores[ 'docbook-xsl-doc'] = '';

//$current="docbook-xsl";  // For debugging

$regex = array();
$regex[ 'ghostscript'     ] = "/^.*current Ghostscript release (\d[\d\.]+) .*$/";
$regex[ 'sane-frontends'  ] = "/^.*Upstream version (\d[\d\.]+\d).*$/";
$regex[ 'xsane'           ] = "/^.*pkgver=(\d[\d\.]+\d).*$/";
$regex[ 'ghostscript-fonts-std' ] =
    "/^.*ghostscript-fonts-std-(\d[\d\.]+\d).tar.*$/";

$url_fix = array (

   array( 'pkg'     => 'cups',
          'match'   => '^.*$',
          'replace' => "https://github.com/apple/cups/releases" ),

   array( 'pkg'     => 'cups-filters',
          'match'   => '^.*$',
          'replace' => "https://www.openprinting.org/download/cups-filters/" ),

   array( 'pkg'     => 'ghostscript',
          'match'   => '^.*$',
          'replace' => "http://www.ghostscript.com/index.html" ),

   array( 'pkg'     => 'ghostscript-fonts-std',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/gs-fonts/files/gs-fonts" ),

   array( 'pkg'     => 'gnu-gs-fonts-other',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/gs-fonts/files/gs-fonts" ),

   array( 'pkg'     => 'gutenprint',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/gimp-print/files" ),

   array( 'pkg'     => 'OpenSP',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/openjade/files" ),

   array( 'pkg'     => 'openjade',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/openjade/files/openjade" ),

   array( 'pkg'     => 'docbook-xml',
          'match'   => '^.*$',
          'replace' => "http://www.docbook.org/schemas/4x.html" ),

   array( 'pkg'     => 'docbook-xsl',
          'match'   => '^.*$',
          'replace' => "https://github.com/docbook/xslt10-stylesheets/releases" ),

//   array( 'pkg'     => 'docbook-xsl-doc',
//          'match'   => '^.*$',
//      'replace' => "http://sourceforge.net/projects/docbook/files/docbook-xsl-doc" ),

   array( 'pkg'     => 'enscript',
          'match'   => '^.*$',
          'replace' => "http://ftp.gnu.org/gnu/enscript" ),

   array( 'pkg'     => 'epdfview',
          'match'   => '^.*$',
          'replace' => "http://anduin.linuxfromscratch.org/sources/BLFS/conglomeration/epdfview" ),

   array( 'pkg'     => 'paps',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/paps/files" ),

   array( 'pkg'     => 'asymptote',
          'match'   => '^.*$',
          'replace' => "http://sourceforge.net/projects/asymptote/files" ),

   array( 'pkg'     => 'biber',
          'match'   => '^.*$',
          'replace' => "https://github.com/plk/biber/releases" ),

   array( 'pkg'     => 'sane-frontends',
          'match'   => '^.*$',
          'replace' => "https://salsa.debian.org/debian/sane-frontends/tags" ),

   array( 'pkg'     => 'xsane',
          'match'   => '^.*$',
          'replace' => "https://git.archlinux.org/svntogit/packages.git/tree/trunk/PKGBUILD?h=packages/xsane" ),
);

function get_packages( $package, $dirpath )
{
  global $regex;
  global $book_index;
  global $url_fix;
  global $current;

  if ( isset( $current ) && $book_index != "$current" ) return 0;

  // These are constant - have not changed in 10 years
  if ( $package == "docbk"             ) return "31";
  if ( $package == "docbook"           ) return "4.5";
  if ( $package == "openjade"          ) return "1.3.2";
  if ( $package == "docbook-dsssl"     ) return "1.79";
  if ( $package == "docbook-dsssl-doc" ) return "1.79";

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

  // Check for ftp
  if ( preg_match( "/^ftp/", $dirpath ) )
  {
     if ( $package == "texlive" )
     {
         $dirpath  = rtrim  ( $dirpath, "/" );    // Trim any trailing slash
         $position = strrpos( $dirpath, "/" );
         $dirpath  = substr ( $dirpath, 0, $position );  // Up 1
         $dirs     = http_get_file( "$dirpath/" );
         $dir      = find_max( $dirs, "/20\d\d/", "/^.*(20\d\d).*$/" );
         $lines    = http_get_file( "$dirpath/$dir/" );
         $ver      = find_max( $lines, "/texlive-/", "/^.*texlive-(\d+.?)-source.*$/" );
         return ( $ver == "0" ) ? "pending" : $ver;
     }

    // Get listing
    $lines = http_get_file( "$dirpath/" );
  }
  else // http
  {
     if ( $book_index == "gnu-gs-fonts-other" )
     {
        $dirs = http_get_file( $dirpath );
        $dir  = find_max( $dirs, "/misc.*GPL/", "/^\s*([\d\.]+.*\))\s+.*$/" );
        $dir  = preg_replace( "/ /",  '%20', $dir );
        $dir  = preg_replace( "/\(/", '%28', $dir );
        $dir  = preg_replace( "/\)/", '%29', $dir );
        $dirpath .= "/$dir";
     }

     $lines = http_get_file( $dirpath );

     if ( ! is_array( $lines ) ) return $lines;
  } // End fetch

  if ( isset( $regex[ $package ] ) )
  {
     // Custom search for latest package name
     foreach ( $lines as $l )
     {
        if ( preg_match( '/^\h*$/', $l ) ) continue;
        $ver = preg_replace( $regex[ $package ], "$1", $l );

        if ( $ver == $l ) continue;
        return $ver;  // Return first match of regex
     }

     return 0;  // This is an error
  }

  if ( $package == "asymptote" )
      return find_max( $lines, "/asymptote/", "/^.*asymptote-([\d\.]+).src.*$/" );

  if ( $package == "cups" )
      return find_max( $lines, "/^.*cups-/", "/^.*cups-([\d\.]+)-source.*$/" );

  if ( $package == "sgml-common" )
      return find_max( $lines, "/sgml-common/", "/^.*sgml-common-([\d\.]+).tgz.*$/" );

  if ( $package == "docbook-xml" )
      return find_max( $lines, "/4\.\d/", "/^.*(4\.\d),.*$/" );

  if ( $package == "docbook-xsl" )
      return find_max( $lines, "/docbook-xsl/", "/^.*docbook-xsl-([\d\.]+).tar.*$/" );

  if ( $package == "psutils" )
      return find_max( $lines, "/$package/", "/^.*$package-(p[\d\.]+).tar.*$/" );

  if ( $package == "fop" )
      return find_max( $lines, "/$package/", "/^.*$package-([\d\.]+)-src.tar.*$/" );

  if ( $package == "texlive" )
      return find_max( $lines, "/$package/", "/^.*$package-([\d\.]+)-source.tar.*$/" );

  if ( $package == "mupdf" )
      return find_max( $lines, "/mupdf/", "/^.*$package-([\d\.]+)-source.tar.*$/" );

  if ( $package == "biblatex" )
      return find_max( $lines, "/biblatex-/", "/^.*$package-([\d\.]+)$/" );

  if ( $package == "biber" )
      return find_max( $lines, "/v\d/", "/^.*v([\d\.]+)$/" );

  if ( $package == "biblatex-biber" )
      return "manual";

  // Most packages are in the form $package-n.n.n
  // Occasionally there are dashes (e.g. 201-1)
  $max = find_max( $lines, "/$package/", "/^.*$package-([\d\.]*\d)\.tar.*$/" );
  return $max;
}

function get_pattern( $line )
{
   $match = array(
//     array( 'pkg'   => 'biblatex-biber',
//            'regex' => "/^.*v(\d[\d\.]+).*$/" ),

     array( 'pkg'   => 'a2ps',
            'regex' => "/^.*a2ps-(\d[\d\.]+).*$/" ),

     array( 'pkg'   => 'i18n-fonts',
            'regex' => "/^.*i18n-fonts-(\d[\d\.]+).*$/" ),

     array( 'pkg'   => 'psutils',
            'regex' => "/^.*psutils-(p\d[\d\.]+).*$/" ),

     array( 'pkg'   => 'texlive.*source',
            'regex' => "/^.*texlive-(\d[\d\.]+.?)-source.*$/" ),
   );

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

   $v = get_packages( $book_index, $url );
   $vers[ $book_index ] = $v;
}

html();  // Write html output
?>
