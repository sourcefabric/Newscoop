-- new columns for month and weekday short names
ALTER TABLE `Languages` ADD COLUMN `ShortMonth1` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth2` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth3` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth4` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth5` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth6` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth7` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth8` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth9` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth10` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth11` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth12` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay1` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay2` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay3` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay4` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay5` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay6` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay7` VARCHAR(20);



CREATE TABLE backup (
    token character varying(64) NOT NULL,
    sessionid character varying(64) NOT NULL,
    status character varying(32) NOT NULL,
    fromtime timestamp NOT NULL,
    totime timestamp NOT NULL,
    PRIMARY KEY (token)
);


CREATE TABLE ls_access (
    gunid bigint,
    token bigint,
    chsum character(32) DEFAULT '' NOT NULL,
    ext character varying(128) DEFAULT '' NOT NULL,
    `type` character varying(20) DEFAULT '' NOT NULL,
    parent bigint,
    `owner` integer,
    ts timestamp,
    INDEX ls_access_gunid_idx (gunid),
    INDEX ls_access_parent_idx (parent),
    INDEX ls_access_token_idx (token)
);


CREATE TABLE ls_classes (
    id integer NOT NULL,
    cname character varying(20),
    PRIMARY KEY (id),
    UNIQUE ls_classes_cname_idx (cname),
    UNIQUE ls_classes_id_idx (id)
);


CREATE TABLE ls_cmemb (
    objid integer NOT NULL,
    cid integer NOT NULL,
    UNIQUE ls_cmemb_idx (objid, cid)
);


CREATE TABLE ls_files (
    id integer NOT NULL,
    gunid bigint NOT NULL,
    name character varying(255) DEFAULT '' NOT NULL,
    mime character varying(255) DEFAULT '' NOT NULL,
    ftype character varying(128) DEFAULT '' NOT NULL,
    state character varying(128) DEFAULT 'empty' NOT NULL,
    currentlyaccessing integer DEFAULT 0 NOT NULL,
    editedby integer,
    mtime timestamp,
    md5 character(32),
    UNIQUE ls_files_gunid_idx (gunid),
    UNIQUE ls_files_id_idx (id),
    INDEX ls_files_md5_idx (md5),
    INDEX ls_files_name_idx (name)
);


CREATE TABLE ls_mdata (
    id integer NOT NULL auto_increment,
    gunid bigint,
    subjns character varying(121),
    subject character varying(212) DEFAULT '' NOT NULL,
    predns character varying(121),
    predicate character varying(212) NOT NULL,
    predxml character(1) DEFAULT 'T' NOT NULL,
    objns character varying(255),
    `object` text,
    INDEX ls_mdata_gunid_idx (gunid),
    UNIQUE ls_mdata_id_idx (id),
    INDEX ls_mdata_pred_idx (predns, predicate),
    INDEX ls_mdata_subj_idx (subjns, subject)
);


CREATE TABLE  `ls_mdata_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE ls_perms (
    permid integer NOT NULL auto_increment,
    subj integer,
    `action` character varying(20),
    obj integer,
    `type` character(1),
    PRIMARY KEY (permid),
    UNIQUE ls_perms_all_idx (subj, `action`, obj),
    UNIQUE ls_perms_permid_idx (permid),
    INDEX ls_perms_subj_obj_idx (subj, obj)
);


CREATE TABLE ls_pref (
    id integer NOT NULL auto_increment,
    subjid integer,
    keystr character varying(255),
    valstr text,
    UNIQUE ls_pref_id_idx (id),
    UNIQUE ls_pref_subj_key_idx (subjid, keystr),
    INDEX ls_pref_subjid_idx (subjid)
);


CREATE TABLE ls_sess (
    sessid character(32) NOT NULL,
    userid integer,
    `login` character varying(255),
    ts timestamp,
    PRIMARY KEY (sessid),
    INDEX ls_sess_login_idx (`login`),
    UNIQUE ls_sess_sessid_idx (sessid),
    INDEX ls_sess_userid_idx (userid)
);


CREATE TABLE ls_smemb (
    id integer NOT NULL auto_increment,
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    `level` integer DEFAULT 0 NOT NULL,
    mid integer,
    PRIMARY KEY (id)
);


CREATE TABLE ls_struct (
    rid integer NOT NULL auto_increment,
    objid integer NOT NULL,
    parid integer NOT NULL,
    `level` integer,
    PRIMARY KEY (rid),
    INDEX ls_struct_level_idx (`level`),
    INDEX ls_struct_objid_idx (objid),
    UNIQUE ls_struct_objid_level_idx (objid, `level`),
    UNIQUE ls_struct_objid_parid_idx (objid, parid),
    INDEX ls_struct_parid_idx (parid),
    UNIQUE ls_struct_rid_idx (rid)
);


CREATE TABLE  `ls_struct_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE ls_subjs (
    id integer NOT NULL auto_increment,
    `login` character varying(255) DEFAULT '' NOT NULL,
    pass character varying(255) DEFAULT '' NOT NULL,
    `type` character(1) DEFAULT 'U' NOT NULL,
    realname character varying(255) DEFAULT '' NOT NULL,
    lastlogin timestamp,
    lastfail timestamp,
    PRIMARY KEY (id),
    UNIQUE ls_subjs_login_idx (`login`)
);


CREATE TABLE ls_trans (
    id integer NOT NULL auto_increment,
    trtok character(16) NOT NULL,
    direction character varying(128) NOT NULL,
    state character varying(128) NOT NULL,
    trtype character varying(128) NOT NULL,
    `lock` character(1) DEFAULT 'N' NOT NULL,
    target character varying(255),
    rtrtok character(16),
    mdtrtok character(16),
    gunid bigint,
    pdtoken bigint,
    url character varying(255),
    localfile character varying(255),
    fname character varying(255),
    title character varying(255),
    expectedsum character(32),
    realsum character(32),
    expectedsize integer,
    realsize integer,
    uid integer,
    errmsg character varying(255),
    jobpid integer,
    `start` timestamp,
    ts timestamp,
    INDEX ls_trans_gunid_idx (gunid),
    UNIQUE ls_trans_id_idx (id),
    INDEX ls_trans_state_idx (state),
    UNIQUE ls_trans_token_idx (pdtoken),
    UNIQUE ls_trans_trtok_idx (trtok)
);


CREATE TABLE ls_tree (
    id integer NOT NULL,
    name character varying(255) DEFAULT '' NOT NULL,
    `type` character varying(255) DEFAULT '' NOT NULL,
    param character varying(255),
    PRIMARY KEY (id),
    INDEX ls_tree_name_idx (name)
);


CREATE TABLE playlog (
    id bigint NOT NULL,
    audioclipid bigint NOT NULL,
    `timestamp` timestamp NOT NULL,
    PRIMARY KEY (id)
);


CREATE TABLE schedule (
    id bigint NOT NULL,
    playlist bigint NOT NULL,
    starts timestamp NOT NULL,
    ends timestamp NOT NULL,
    PRIMARY KEY (id)
);


INSERT INTO ls_perms VALUES (1, 1, '_all', 1, 'A');
INSERT INTO ls_perms VALUES (2, 2, 'read', 1, 'A');
INSERT INTO ls_perms VALUES (3, 4, '_all', 3, 'A');
INSERT INTO ls_perms VALUES (4, 4, 'read', 1, 'A');
INSERT INTO ls_perms VALUES (6, 7, '_all', 7, 'A');


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



INSERT INTO ls_smemb VALUES (1, 4, 3, 0, NULL);
INSERT INTO ls_smemb VALUES (2, 4, 2, 0, NULL);
INSERT INTO ls_smemb VALUES (3, 4, 1, 0, NULL);
INSERT INTO ls_smemb VALUES (4, 5, 2, 0, NULL);
INSERT INTO ls_smemb VALUES (7, 7, 3, 0, NULL);
INSERT INTO ls_smemb VALUES (8, 7, 2, 0, NULL);
INSERT INTO ls_smemb VALUES (9, 7, 1, 0, NULL);



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



INSERT INTO ls_tree VALUES (1, 'RootNode', 'RootNode', NULL);
INSERT INTO ls_tree VALUES (2, 'StorageRoot', 'Folder', NULL);
INSERT INTO ls_tree VALUES (3, 'root', 'Folder', NULL);
INSERT INTO ls_tree VALUES (4, 'trash_', 'Folder', NULL);
INSERT INTO ls_tree VALUES (5, '04. Vangelis - Song Of White.mp3', 'audioclip', NULL);
INSERT INTO ls_tree VALUES (6, 'admin', 'Folder', NULL);
INSERT INTO ls_tree VALUES (7, 'admin', 'Folder', NULL);



ALTER TABLE ls_access
    ADD CONSTRAINT ls_access_owner_fkey FOREIGN KEY (`owner`) REFERENCES ls_subjs(id);


--
-- Name: ls_files_editedby_fkey; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ls_files
    ADD CONSTRAINT ls_files_editedby_fkey FOREIGN KEY (editedby) REFERENCES ls_subjs(id);


--
-- Name: ls_perms_subj_fkey; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ls_perms
    ADD CONSTRAINT ls_perms_subj_fkey FOREIGN KEY (subj) REFERENCES ls_subjs(id) ON DELETE CASCADE;


--
-- Name: ls_pref_subjid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ls_pref
    ADD CONSTRAINT ls_pref_subjid_fkey FOREIGN KEY (subjid) REFERENCES ls_subjs(id) ON DELETE CASCADE;


--
-- Name: ls_sess_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ls_sess
    ADD CONSTRAINT ls_sess_userid_fkey FOREIGN KEY (userid) REFERENCES ls_subjs(id) ON DELETE CASCADE;


--
-- Name: ls_struct_objid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ls_struct
    ADD CONSTRAINT ls_struct_objid_fkey FOREIGN KEY (objid) REFERENCES ls_tree(id) ON DELETE CASCADE;


--
-- Name: ls_struct_parid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ls_struct
    ADD CONSTRAINT ls_struct_parid_fkey FOREIGN KEY (parid) REFERENCES ls_tree(id) ON DELETE CASCADE;
