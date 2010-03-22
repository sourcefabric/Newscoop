-- add new system preferences for tinymce editor image resizing and zooming
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('EditorImageResizeWidth', '');
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('EditorImageResizeHeight', '');
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('EditorImageZoom', 'N');


-- Initialize the topics order field
SET @i:=0;
CREATE TEMPORARY TABLE `TopicsTmp` SELECT DISTINCT `Id`, `LanguageId`, `Name`, `TopicOrder` FROM `Topics`;
DELETE FROM `TopicsTmp` WHERE 
    Id IN (SELECT Id FROM `Topics` GROUP BY Id HAVING COUNT(LanguageId) > 1) 
    AND LanguageId NOT IN (SELECT LanguageId FROM `Topics` GROUP BY Id HAVING COUNT(LanguageId) > 1);
UPDATE `TopicsTmp` SET `TopicOrder`= @i:=@i+1 ORDER BY Id ASC, LanguageId ASC, Name ASC;
UPDATE `Topics` SET `TopicOrder`= (SELECT `TopicsTmp`.`TopicOrder` FROM `TopicsTmp` WHERE `TopicsTmp`.Id = `Topics`.Id);


INSERT INTO ls_perms VALUES (1, 1, '_all', 1, 'A');
INSERT INTO ls_perms VALUES (2, 2, 'read', 1, 'A');
INSERT INTO ls_perms VALUES (3, 4, '_all', 3, 'A');
INSERT INTO ls_perms VALUES (4, 4, 'read', 1, 'A');
INSERT INTO ls_perms VALUES (6, 7, '_all', 7, 'A');


INSERT INTO ls_perms_id_seq_seq VALUES (6);


INSERT INTO ls_pref VALUES (1, 3, 'stationName', 'Radio Station 1');
INSERT INTO ls_pref VALUES (2, 3, 'genres', '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE options [
 <!ELEMENT options   (option*) >
 <!ELEMENT option    (label+) >
 <!ELEMENT label     (#PCDATA) >
 <!ATTLIST option    id           CDATA   #REQUIRED >
 <!ATTLIST label     xml:lang     CDATA   #IMPLIED >
]>
<options>
 <option id="Blues"><label>Blues</label></option>
 <option id="Classic Rock"><label>Classic Rock</label></option>
 <option id="Country"><label>Country</label></option>
 <option id="Dance"><label>Dance</label></option>
 <option id="Disco"><label>Disco</label></option>
 <option id="Funk"><label>Funk</label></option>
 <option id="Grunge"><label>Grunge</label></option>
 <option id="Hip-Hop"><label>Hip-Hop</label></option>
 <option id="Jazz"><label>Jazz</label></option>
 <option id="Metal"><label>Metal</label></option>
 <option id="New Age"><label>New Age</label></option>
 <option id="Oldies"><label>Oldies</label></option>
 <option id="Other"><label>Other</label></option>
 <option id="Pop"><label>Pop</label></option>
 <option id="R&amp;B"><label>R&amp;B</label></option>
 <option id="Rap"><label>Rap</label></option>
 <option id="Reggae"><label>Reggae</label></option>
 <option id="Rock"><label>Rock</label></option>
 <option id="Techno"><label>Techno</label></option>
 <option id="Industrial"><label>Industrial</label></option>
 <option id="Alternative"><label>Alternative</label></option>
 <option id="Ska"><label>Ska</label></option>
 <option id="Death Metal"><label>Death Metal</label></option>
 <option id="Pranks"><label>Pranks</label></option>
 <option id="Soundtrack"><label>Soundtrack</label></option>
 <option id="Euro-Techno"><label>Euro-Techno</label></option>
 <option id="Ambient"><label>Ambient</label></option>
 <option id="Trip-Hop"><label>Trip-Hop</label></option>
 <option id="Vocal"><label>Vocal</label></option>
 <option id="Jazz+Funk"><label>Jazz+Funk</label></option>
 <option id="Fusion"><label>Fusion</label></option>
 <option id="Trance"><label>Trance</label></option>
 <option id="Classical"><label>Classical</label></option>
 <option id="Instrumental"><label>Instrumental</label></option>
 <option id="Acid"><label>Acid</label></option>
 <option id="House"><label>House</label></option>
 <option id="Game"><label>Game</label></option>
 <option id="Sound Clip"><label>Sound Clip</label></option>
 <option id="Gospel"><label>Gospel</label></option>
 <option id="Noise"><label>Noise</label></option>
 <option id="AlternRock"><label>AlternRock</label></option>
 <option id="Bass"><label>Bass</label></option>
 <option id="Soul"><label>Soul</label></option>
 <option id="Punk"><label>Punk</label></option>
 <option id="Space"><label>Space</label></option>
 <option id="Meditative"><label>Meditative</label></option>
 <option id="Instrumental Pop"><label>Instrumental Pop</label></option>
 <option id="Instrumental Rock"><label>Instrumental Rock</label></option>
 <option id="Ethnic"><label>Ethnic</label></option>
 <option id="Gothic"><label>Gothic</label></option>
 <option id="Darkwave"><label>Darkwave</label></option>
 <option id="Techno-Industrial"><label>Techno-Industrial</label></option>
 <option id="Electronic"><label>Electronic</label></option>
 <option id="Pop-Folk"><label>Pop-Folk</label></option>
 <option id="Eurodance"><label>Eurodance</label></option>
 <option id="Dream"><label>Dream</label></option>
 <option id="Southern Rock"><label>Southern Rock</label></option>
 <option id="Comedy"><label>Comedy</label></option>
 <option id="Cult"><label>Cult</label></option>
 <option id="Gangsta"><label>Gangsta</label></option>
 <option id="Top 40"><label>Top 40</label></option>
 <option id="Christian Rap"><label>Christian Rap</label></option>
 <option id="Pop/Funk"><label>Pop/Funk</label></option>
 <option id="Jungle"><label>Jungle</label></option>
 <option id="Native American"><label>Native American</label></option>
 <option id="Cabaret"><label>Cabaret</label></option>
 <option id="New Wave"><label>New Wave</label></option>
 <option id="Psychadelic"><label>Psychadelic</label></option>
 <option id="Rave"><label>Rave</label></option>
 <option id="Showtunes"><label>Showtunes</label></option>
 <option id="Trailer"><label>Trailer</label></option>
 <option id="Lo-Fi"><label>Lo-Fi</label></option>
 <option id="Tribal"><label>Tribal</label></option>
 <option id="Acid Punk"><label>Acid Punk</label></option>
 <option id="Acid Jazz"><label>Acid Jazz</label></option>
 <option id="Polka"><label>Polka</label></option>
 <option id="Retro"><label>Retro</label></option>
 <option id="Musical"><label>Musical</label></option>
 <option id="Rock &amp; Roll"><label>Rock &amp; Roll</label></option>
 <option id="Hard Rock"><label>Hard Rock</label></option>
 <option id="Folk"><label>Folk</label></option>
 <option id="Folk-Rock"><label>Folk-Rock</label></option>
 <option id="National Folk"><label>National Folk</label></option>
 <option id="Swing"><label>Swing</label></option>
 <option id="Fast Fusion"><label>Fast Fusion</label></option>
 <option id="Bebob"><label>Bebob</label></option>
 <option id="Latin"><label>Latin</label></option>
 <option id="Revival"><label>Revival</label></option>
 <option id="Celtic"><label>Celtic</label></option>
 <option id="Bluegrass"><label>Bluegrass</label></option>
 <option id="Avantgarde"><label>Avantgarde</label></option>
 <option id="Gothic Rock"><label>Gothic Rock</label></option>
 <option id="Progressive Rock"><label>Progressive Rock</label></option>
 <option id="Psychedelic Rock"><label>Psychedelic Rock</label></option>
 <option id="Symphonic Rock"><label>Symphonic Rock</label></option>
 <option id="Slow Rock"><label>Slow Rock</label></option>
 <option id="Big Band"><label>Big Band</label></option>
 <option id="Chorus"><label>Chorus</label></option>
 <option id="Easy Listening"><label>Easy Listening</label></option>
 <option id="Acoustic"><label>Acoustic</label></option>
 <option id="Humour"><label>Humour</label></option>
 <option id="Speech"><label>Speech</label></option>
 <option id="Chanson"><label>Chanson</label></option>
 <option id="Opera"><label>Opera</label></option>
 <option id="Chamber Music"><label>Chamber Music</label></option>
 <option id="Sonata"><label>Sonata</label></option>
 <option id="Symphony"><label>Symphony</label></option>
 <option id="Booty Bass"><label>Booty Bass</label></option>
 <option id="Primus"><label>Primus</label></option>
 <option id="Porn Groove"><label>Porn Groove</label></option>
 <option id="Satire"><label>Satire</label></option>
 <option id="Slow Jam"><label>Slow Jam</label></option>
 <option id="Club"><label>Club</label></option>
 <option id="Tango"><label>Tango</label></option>
 <option id="Samba"><label>Samba</label></option>
 <option id="Folklore"><label>Folklore</label></option>
 <option id="Ballad"><label>Ballad</label></option>
 <option id="Power Ballad"><label>Power Ballad</label></option>
 <option id="Rhythmic Soul"><label>Rhythmic Soul</label></option>
 <option id="Freestyle"><label>Freestyle</label></option>
 <option id="Duet"><label>Duet</label></option>
 <option id="Punk Rock"><label>Punk Rock</label></option>
 <option id="Drum Solo"><label>Drum Solo</label></option>
 <option id="A capella"><label>A capella</label></option>
 <option id="Euro-House"><label>Euro-House</label></option>
 <option id="Dance Hall"><label>Dance Hall</label></option>
</options>
');
INSERT INTO ls_pref VALUES (3, 3, 'stationFrequency', '');
INSERT INTO ls_pref VALUES (4, 3, 'stationLogoPath', 'img/stationlogo.image');
INSERT INTO ls_pref VALUES (5, 3, 'stationURL', 'http://');
INSERT INTO ls_pref VALUES (6, 3, 'scratchpadMaxlength', '10');
INSERT INTO ls_pref VALUES (7, 4, 'scratchpadContents', '');


INSERT INTO ls_pref_id_seq_seq VALUES (7);


INSERT INTO ls_smemb VALUES (1, 4, 3, 0, NULL);
INSERT INTO ls_smemb VALUES (2, 4, 2, 0, NULL);
INSERT INTO ls_smemb VALUES (3, 4, 1, 0, NULL);
INSERT INTO ls_smemb VALUES (4, 5, 2, 0, NULL);
INSERT INTO ls_smemb VALUES (7, 7, 3, 0, NULL);
INSERT INTO ls_smemb VALUES (8, 7, 2, 0, NULL);
INSERT INTO ls_smemb VALUES (9, 7, 1, 0, NULL);


INSERT INTO ls_smemb_id_seq_seq VALUES (9);


INSERT INTO ls_struct VALUES (1, 2, 1, 1);
INSERT INTO ls_struct VALUES (2, 3, 2, 1);
INSERT INTO ls_struct VALUES (3, 3, 1, 2);
INSERT INTO ls_struct VALUES (4, 4, 2, 1);
INSERT INTO ls_struct VALUES (5, 4, 1, 2);
INSERT INTO ls_struct VALUES (6, 5, 3, 1);
INSERT INTO ls_struct VALUES (7, 5, 2, 2);
INSERT INTO ls_struct VALUES (8, 5, 1, 3);
INSERT INTO ls_struct VALUES (11, 6, 4, 1);
INSERT INTO ls_struct VALUES (12, 6, 2, 2);
INSERT INTO ls_struct VALUES (13, 6, 1, 3);
INSERT INTO ls_struct VALUES (14, 7, 2, 1);
INSERT INTO ls_struct VALUES (15, 7, 1, 2);


INSERT INTO ls_struct_id_seq_seq VALUES (15);


INSERT INTO ls_subjs VALUES (1, 'Admins', '!', 'G', '', NULL, NULL);
INSERT INTO ls_subjs VALUES (2, 'All', '!', 'G', '', NULL, NULL);
INSERT INTO ls_subjs VALUES (3, 'StationPrefs', '!', 'G', '', NULL, NULL);
INSERT INTO ls_subjs VALUES (5, 'scheduler', 'bbbb2edef660739a6071ab5a4f8a869f', 'U', '', NULL, NULL);
INSERT INTO ls_subjs VALUES (4, 'root', '7694f4a66316e53c8cdd9d9954bd611d', 'U', '', '2009-07-14 16:15:13.523947', NULL);
INSERT INTO ls_subjs VALUES (7, 'admin', '5e8f0b702f365224d31084ee4f74b576', 'U', '', '2009-07-26 21:26:03.253313', NULL);


INSERT INTO ls_subjs_id_seq_seq VALUES (7);


INSERT INTO ls_tree VALUES (1, 'RootNode', 'RootNode', NULL);
INSERT INTO ls_tree VALUES (2, 'StorageRoot', 'Folder', NULL);
INSERT INTO ls_tree VALUES (3, 'root', 'Folder', NULL);
INSERT INTO ls_tree VALUES (4, 'trash_', 'Folder', NULL);
INSERT INTO ls_tree VALUES (5, '04. Vangelis - Song Of White.mp3', 'audioclip', NULL);
INSERT INTO ls_tree VALUES (7, 'admin', 'Folder', NULL);


INSERT INTO ls_tree_id_seq_seq VALUES (7);
