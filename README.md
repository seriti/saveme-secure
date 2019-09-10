# Saveme secure module. 

## Designed for securely managing sensitive informations.

Use this module to manage logins, notes, documents. You are asked to create an encryption key on first use 
which is then used to encrypt all logins and notes (except name and keywords fields). 
Documents are not encrypted by default but on demand.

## Requires Seriti Slim 3 MySQL Framework skeleton

This module integrates seamlessly into [Seriti skeleton framework](https://github.com/seriti/slim3-skeleton).  
You need to first install the skeleton framework and then download the source files for the module and follow these instructions.

It is possible to use this module independantly from the seriti skeleton but you will still need the [Seriti tools library](https://github.com/seriti/tools).  
It is strongly recommended that you first install the seriti skeleton to see a working example of code use before using it within another application framework.  
That said, if you are an experienced PHP programmer you will have no problem doing this and the required code footprint is very small.  

## Install the module

1.) Install Seriti Skeleton framework(see the framework readme for detailed instructions):   
    **composer create-project seriti/slim3-skeleton [directory-for-app]**.   
    Make sure that you have thsi working before you proceed.

2.) Download a copy of Saveme-secure module source code directly from github and unzip,  
or by using **git clone https://github.com/seriti/saveme-secure** from command line.  
Once you have a local copy of module code check that it has following structure:

/Saveme/(all module implementation classes are in this folder)  
/setup_add.php  
/routes.php  

3.) Copy the **Saveme** folder and all its contents into **[directory-for-app]/app** folder.

4.) Open the routes.php file and insert the **$this->group('/saveme', function (){}** route definition block
within the existing  **$app->group('/admin', function () {}** code block contained in existing skeleton **[directory-for-app]/src/routes.php** file.

5.) Open the setup_app.php file and  add the module config code snippet into bottom of skeleton **[directory-for-app]/src/setup_app.php** file.  
Please check the **table_prefix** value to ensure that there will not be a clash with any existing tables in your database.

6.) Copy the contents of "templates" folder to **[directory-for-app]/templates/** folder
 
7.) Now in your browser goto URL:  

"http://localhost:8000/admin/saveme/dashboard" if you are using php built in server  
OR  
"http://www.yourdomain.com/admin/saveme/dashboard" if you have configured a domain on your server  

Now click link at bottom of page **Setup Database**: This will create all necessary database tables with table_prefix as defined above.  
Thats it, you are good to go. Add a login or note and you will be prompted to input your encryptions key.

NB1: This encryption key is as secure as you make it(a basic minimum security level is enforced).   
It is never stored in the clear anywhere, not in the browser, not on the server. 
The server keeps a temporary encrypted version of your key and the browser is issued a temporary key for this. 
After 24hrs or when you logout both these temporary keys are erased and you need to recapture key.  
NB2: never use this module outside https:// protocol.  
NB3: If you forget your encryption key there is no way to recover it so keep a hard copy somewhere.

## Disclaimer

You must check that you are happy with security of this module and how encryption is handled. 
While everything has been done to ensure a secure as possible setup, NO guarantee is provided.