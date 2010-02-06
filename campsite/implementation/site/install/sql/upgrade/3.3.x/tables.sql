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


CREATE TABLE `Archive_FileMetadata` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `gunid` varchar(20) NOT NULL default '0',
  `predicate_ns` varchar(10) default '',
  `predicate` varchar(30) NOT NULL default '',
  `object` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gunid_tag_id` (`gunid`,`predicate_ns`,`predicate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `Attachments` RENAME `Attachments_Old`;


CREATE TABLE `Attachments` (
  `gunid` VARCHAR(20)  NOT NULL,
  `file_name` varchar(255) default NULL,
  `extension` varchar(50) default NULL,
  `mime_type` varchar(255) default NULL,
  `size_in_bytes` bigint(20) unsigned default NULL,
  `fk_user_id` int(10) unsigned default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`gunid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `ArticleAttachments` RENAME `ArticleAttachments_Old`;


CREATE TABLE `ArticleAttachments` (
  `fk_article_number` INTEGER UNSIGNED NOT NULL,
  `fk_file_gunid` VARCHAR(20)  NOT NULL,
  `fk_language_id` INTEGER UNSIGNED,
  PRIMARY KEY (`fk_article_number`, `fk_file_gunid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `ArticleImages` RENAME `ArticleImages_Old`;


CREATE TABLE  `ArticleImages` (
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_file_gunid` int(10) unsigned NOT NULL DEFAULT '0',
  `image_index` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_article_number`,`fk_file_gunid`),
  UNIQUE KEY `ArticleImage` (`fk_article_number`,`image_index`),
  KEY `ArticleImages_file_gunid_idx` (`fk_file_gunid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE backup (
    token character varying(64) NOT NULL,
    sessionid character varying(64) NOT NULL,
    status character varying(32) NOT NULL,
    fromtime timestamp NOT NULL,
    totime timestamp NOT NULL,
    PRIMARY KEY (token)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE ls_classes (
    id integer NOT NULL,
    cname character varying(20),
    PRIMARY KEY (id),
    UNIQUE ls_classes_cname_idx (cname),
    UNIQUE ls_classes_id_idx (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_classes_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE ls_cmemb (
    objid integer NOT NULL,
    cid integer NOT NULL,
    UNIQUE ls_cmemb_idx (objid, cid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_mdata_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  ls_perms_id_seq_seq (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE ls_pref (
    id integer NOT NULL auto_increment,
    subjid integer,
    keystr character varying(255),
    valstr text,
    UNIQUE ls_pref_id_idx (id),
    UNIQUE ls_pref_subj_key_idx (subjid, keystr),
    INDEX ls_pref_subjid_idx (subjid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_pref_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE ls_sess (
    sessid character(32) NOT NULL,
    userid integer,
    `login` character varying(255),
    ts timestamp,
    PRIMARY KEY (sessid),
    INDEX ls_sess_login_idx (`login`),
    UNIQUE ls_sess_sessid_idx (sessid),
    INDEX ls_sess_userid_idx (userid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE ls_smemb (
    id integer NOT NULL auto_increment,
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    `level` integer DEFAULT 0 NOT NULL,
    mid integer,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_smemb_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_struct_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_subjs_id_seq_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE ls_tree (
    id integer NOT NULL,
    name character varying(255) DEFAULT '' NOT NULL,
    `type` character varying(255) DEFAULT '' NOT NULL,
    param character varying(255),
    PRIMARY KEY (id),
    INDEX ls_tree_name_idx (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `ls_tree_id_seq_seq` (
    `id` int(10) unsigned NOT NULL auto_increment,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE playlog (
    id bigint NOT NULL,
    audioclipid bigint NOT NULL,
    `timestamp` timestamp NOT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE schedule (
    id bigint NOT NULL,
    playlist bigint NOT NULL,
    starts timestamp NOT NULL,
    ends timestamp NOT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
