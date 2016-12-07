# Tool to move from 1Password to Keepassx

1. Export passwords from 1Password as CSV with the following columns: note, password, title, type, url, username, created date, modified date. Name the file export.csv and place it in the same directory as this script.

2. Run this tool.
    
        php convert.php > export.xml

3. Use KeeWeb to import it: https://keeweb.info/ and save the file as .kdbx database.
4. Unfortunately, KeeWeb creates in its current version a database that cannot be opened by Keepassx. Open it in MacPass (https://github.com/mstarke/MacPass) once and save the database. Keepassx is then able to open it.

I haven't tried to do the CSV import in Keepass, this would probably save steps 3 and 4.

To extend the XML import please check KeeWebs example xml file: https://github.com/keeweb/kdbxweb/blob/master/resources/demo.xml
