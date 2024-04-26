<?php /*
	// ~ first complete editing columns of table as much as you can (as much as you can means as much as it possible to do without network for example file manager is not finished yet so you can't do it yet)
	// 	@ do it part by part start from yes no question and complete fixing buttons and everything about it
	// 		//$ showing the name or title of options (yes/no) in before and end of switch button
	// 		//$ showing fa icon inside switch button
	// 		//$ enable reset power to reset it to first place (yes or no)
	// 		//$ make it 0/false on clear
	// 		//$ copy yes no question is not work well
	~ select box option has bug (multiple)
		@ multiple option should be once and if its enabled it means all of options are selectble and if its disabled it means only one option is selectble
			// $ move multiple checkbox from second part to first part if the column is select box
			//$ remove it from the table of list of options {
				//^ data json and javascript datatable {columns list} and html code
			//$ }
			//$ remove it from database
			//$ remove it from saver scripts
			//!delete select option settings when column and table is deleting too
			//$ copy select option is not working on options and setting
			^ add move power for options
			^ add delete all options and optgroups button
	// ~ password encoder
	// 	@ fix things about it
	// 		$ stop showing it on tables and editing and ...
	// 		$ add encoder saver for it
	// 		^ add button which will ask do you wanna change the password or keep the last one in editing !
	~ fix all of $.post and $.get
		@ add .done and .fail and .always
			$ check the bugs which might happen while client is out of internet
			$ make the operation fail and show the swal fire alert with error message
	~ bug reporter for errors
		@ a function which will save bugs in database and send it to technosha before save by admin permission
			$ php errors
			$ javascript and jquery errors
			$ $.post and $.get and .load errors
	~ system log
		@ update logSaver function and add log saver for html and javascript too
			$ save each php operation
			$ save each page loading
			$ save each javascript operation
			$ save admin or client full information (ip,os,time)
			$ search on internet and find way to get all php errors like how xampp works for save the errors
			$ add a nice place to control the logs and see everything and make the place full customizble

	~ swal fire yes no questions has bug on cancel or no
		@ fix the bugs if client didn't say yes or accept
			$ search this things and fix the bugs
				& }).then((result) => {
				& if(result.value) {

	// ~ edit tables data allinone buttons option not working
	// 	@ buttons in end of editing data of table not working
	// 		$ delete all
	// 		$ recovery all
	// 		$ delete all
	// 		$ save all
	// 		$ save all and close

	~ file manager
		@ last modified and sizes
			//$ change last modified and size of folders which file uploaded in
			// $ if folder not exist it must be return to home or show error but its not doing it !
			// $ cutFilemanagerItem
			// $ copyFilemanagerItem
			// $ deleteFilemanagerItem
			// $ renameFilemanagerItem
			// $ downloadFilemanagerItem
			// $ pasteFilemanageItem
			$ multiple data to do operations (copy,cut,paste,delete,rename,...)


	. yek proccessor baraye mohasebeye zamane baghi mande va darsade baghimande baraye anjame amaliat baraye ghesmate permission ha niaz ast
	. ghesmate selectbox yek jaye khali dare ke ghablan multiple option bod va az onja pak shud va hala mishe be jaye on multiple option ye box ezafe kard tosh kalamate kilidi baraye josto jo type kard va kheyli mofid hast
*/?>