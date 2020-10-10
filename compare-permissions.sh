#!/usr/bin/perl

use File::Find;

my $directory1 = '/home/user/dir1';
my $directory2 = '/home/user/dir2';

find(\&hashfiles, $directory1);

sub hashfiles {
  my $file1 = $File::Find::name;
  (my $file2 = $file1) =~ s/^$directory1/$directory2/;

  my $mode1 = (stat($file1))[2] ;
  my $mode2 = (stat($file2))[2] ;

  my $uid1 = (stat($file1))[4] ;
  my $uid2 = (stat($file2))[4] ;

  print "Permissions for $file1 and $file2 are not the same\n" if ( $mode1 != $mode2 );
  print "Ownership for $file1 and $file2 are not the same\n" if ( $uid1 != $uid2 );
}
