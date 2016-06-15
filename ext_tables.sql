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
