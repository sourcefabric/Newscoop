#! /usr/bin/perl -w
# $Id: make-release.pl,v 1.1 2004/09/11 02:43:24 paul Exp $

# Script for creating a distribution archive.  Based on make-release.pl from
# jscalendar.

# Author: Mihai Bazon, http://dynarch.com/mishoo
# NO WARRANTIES WHATSOEVER.  READ GNU LGPL.

# This file requires HTML::Mason; this module is used for automatic
# substitution of the version/release number as well as for selection of the
# changelog (at least in the file release-notes.html).  It might not work
# without HTML::Mason.

use strict;
# use diagnostics;
use HTML::Mason;
use File::Find;
use XML::Parser;
use Data::Dumper;

my $verbosity = 1;

my $tmpdir = '/tmp';

my $config = parseXML("project-config.xml");
speak(3, Data::Dumper::Dumper($config));

my ($project, $version, $release, $basename);

$project = $config->{project}{ATTR}{title};
$version = $config->{project}{version}{DATA};
$release = $config->{project}{release}{DATA};
$basename = "$project-$version";
$basename .= "-$release" if ($release);

speak(1, "Project: $basename");

## create directory tree
my ($basedir);
{
    # base directory
    $basedir = "$tmpdir/$basename";
    if (-d $basedir) {
        speak(-1, "$basedir already exists, removing... >:-]\n");
        system "rm -rf $basedir";
    }
}

process_directory();

## make the ZIP file
chdir "$basedir/..";
speak(1, "Making ZIP file /tmp/$basename.zip");
system ("zip -r $basename.zip $basename > /dev/null");
system ("ls -la /tmp/$basename.zip");

## remove the basedir
system("rm -rf $basedir");

## back
#chdir $cwd;



### SUBROUTINES

# handle _one_ file
sub process_one_file {
    my ($attr, $target) = @_;

    $target =~ s/\/$//;
    $target .= '/';
    my $destination = $target.$attr->{REALNAME};

    # copy file first
    speak(1, "   copying $attr->{REALNAME}");
    system "cp $attr->{REALNAME} $destination";

    my $masonize = $attr->{masonize} || '';
    if ($masonize =~ /yes|on|1/i) {
        speak(1, "   > masonizing to $destination...");
        my $args = $attr->{args} || '';
        my @vars = split(/\s*,\s*/, $args);
        my %args = ();
        foreach my $i (@vars) {
            $args{$i} = eval '$'.$i;
            speak(1, "      > argument: $i => $args{$i}");
        }
        my $outbuf;
        my $interp = HTML::Mason::Interp->new ( comp_root    => $target,
                                                out_method   => \$outbuf );
        $interp->exec("/$attr->{REALNAME}", %args);
        open (FILE, "> $destination");
        print FILE $outbuf;
        close (FILE);
    }
}

# handle some files
sub process_files {
    my ($files, $target) = @_;

    # proceed with the explicitely required files first
    my %options = ();
    foreach my $i (@{$files}) {
        $options{$i->{ATTR}{name}} = $i->{ATTR};
    }

    foreach my $i (@{$files}) {
        my @expanded = glob "$i->{ATTR}{name}";
        foreach my $file (@expanded) {
            $i->{ATTR}{REALNAME} = $file;
            if (defined $options{$file}) {
                unless (defined $options{$file}->{PROCESSED}) {
                    speak(1, "EXPLICIT FILE: $file");
                    $options{$file}->{REALNAME} = $file;
                    process_one_file($options{$file}, $target);
                    $options{$file}->{PROCESSED} = 1;
                }
            } else {
                speak(2, "GLOB: $file");
                process_one_file($i->{ATTR}, $target);
                $options{$file} = 2;
            }
        }
    }
}

# handle _one_ directory
sub process_directory {
    my ($dir, $path) = @_;
    my $cwd = '..';             # ;-)

    (defined $dir) || ($dir = '.');
    (defined $path) || ($path = '');
    speak(2, "DIR: $path$dir");
    $dir =~ s/\/$//;
    $dir .= '/';

    unless (-d $dir) {
        speak(-1, "DIRECTORY '$dir' NOT FOUND, SKIPPING");
        return 0;
    }

    # go where we have stuff to do
    chdir $dir;

    my $target = $basedir;
    ($path =~ /\S/) && ($target .= "/$path");
    ($dir ne './') && ($target .= $dir);

    speak(1, "*** Creating directory: $target");
    mkdir $target;

    unless (-f 'makefile.xml') {
        speak(-1, "No makefile.xml in this directory");
        chdir $cwd;
        return 0;
    }
    my $config = parseXML("makefile.xml");
    speak(3, Data::Dumper::Dumper($config));

    my $tmp = $config->{files}{file};
    if (defined $tmp) {
        my $files;
        if (ref($tmp) eq 'ARRAY') {
            $files = $tmp;
        } else {
            $files = [ $tmp ];
        }
        process_files($files, $target);
    }

    $tmp = $config->{files}{dir};
    if (defined $tmp) {
        my $subdirs;
        if (ref($tmp) eq 'ARRAY') {
            $subdirs = $tmp;
        } else {
            $subdirs = [ $tmp ];
        }
        foreach my $i (@{$subdirs}) {
            process_directory($i->{ATTR}{name}, $path.$dir);
        }
    }

    # get back to our previous location
    chdir $cwd;
}

# this does all the XML parsing shit we'll need for our little task
sub parseXML {
    my ($filename) = @_;
    my $rethash = {};

    my @tagstack;

    my $handler_start = sub {
        my ($parser, $tag, @attrs) = @_;
        my $current_tag = {};
        $current_tag->{NAME} = $tag;
        $current_tag->{DATA} = '';
        push @tagstack, $current_tag;
        if (scalar @attrs) {
            my $attrs = {};
            $current_tag->{ATTR} = $attrs;
            while (scalar @attrs) {
                my $name = shift @attrs;
                my $value = shift @attrs;
                $attrs->{$name} = $value;
            }
        }
    };

    my $handler_char = sub {
        my ($parser, $data) = @_;
        if ($data =~ /\S/) {
            $tagstack[$#tagstack]->{DATA} .= $data;
        }
    };

    my $handler_end = sub {
        my $current_tag = pop @tagstack;
        if (scalar @tagstack) {
            my $tmp = $tagstack[$#tagstack]->{$current_tag->{NAME}};
            if (defined $tmp) {
                ## better build an array, there are more elements with this tagname
                if (ref($tmp) eq 'ARRAY') {
                    ## oops, the ARRAY is already there, just add the new element
                    push @{$tmp}, $current_tag;
                } else {
                    ## create the array "in-place"
                    $tagstack[$#tagstack]->{$current_tag->{NAME}} = [ $tmp, $current_tag ];
                }
            } else {
                $tagstack[$#tagstack]->{$current_tag->{NAME}} = $current_tag;
            }
        } else {
            $rethash->{$current_tag->{NAME}} = $current_tag;
        }
    };

    my $parser = new XML::Parser
      ( Handlers => { Start => $handler_start,
                      Char  => $handler_char,
                      End   => $handler_end } );
    $parser->parsefile($filename);

    return $rethash;
}

# print somethign according to the level of verbosity
# receives: verbosity_level and message
# prints message if verbosity_level >= $verbosity (global)
sub speak {
    my ($v, $t) = @_;
    if ($v < 0) {
        print STDERR "\033[1;31m!! $t\033[0m\n";
    } elsif ($verbosity >= $v) {
        print $t, "\n";
    }
}
