#
# Table structure for table 'tx_error404page_domain_model_error'
#
CREATE TABLE tx_error404page_domain_model_error (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	sha1 varchar(255) DEFAULT '' NOT NULL,
	url text NOT NULL,
	reason text NOT NULL,
	last_referer text NOT NULL,
	counter int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);
