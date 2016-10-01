#
# Table structure for table 'tx_error404page_domain_model_error'
#
CREATE TABLE tx_error404page_domain_model_error (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	url text NOT NULL,
	url_hash varchar(255) DEFAULT '' NOT NULL,
	root_page int(11) DEFAULT '0' NOT NULL,
	reason text NOT NULL,
	counter int(11) DEFAULT '0' NOT NULL,
	referer text NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	user_agent varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);

#
# Table structure for table 'cf_error404page_errorhandler'
#
CREATE TABLE cf_error404page_errorhandler (
	id int(11) unsigned NOT NULL auto_increment,
	identifier varchar(250) DEFAULT '' NOT NULL,
	expires int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	content mediumblob,
	lifetime int(11) unsigned DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY cache_id (identifier)
);
#
# Table structure for table 'cf_error404page_errorhandler_tags'
#
CREATE TABLE cf_error404page_errorhandler_tags (
	id int(11) unsigned NOT NULL auto_increment,
	identifier varchar(250) DEFAULT '' NOT NULL,
	tag varchar(250) DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	KEY cache_id (identifier),
	KEY cache_tag (tag)
);