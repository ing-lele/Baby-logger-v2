### IngLele Fork at 26 Jun 2022 from https://github.com/tommygober/Baby-logger
# =========================================================
# Scope:
# -- Export all SQL data to CSV file for backup
#
# =========================================================

# IMPORT STATEMENTS
from turtle import end_fill
import os
import sys
import csv
import time
import datetime
import pymysql
pymysql.install_as_MySQLdb()
import MySQLdb
import mysql_variables  #Import MySQL variable

debug_on = 1            #DEBUG - Enable debug print

#----------------------------------------------------------
# MySQL variable are defined in mysql_variables.py module
# MySQLdb.db_host 
# MySQLdb.db_user 
# MySQLdb.db_pass 
# MySQLdb.db_name 

#----------------------------------------------------------
#Setup DB
if(debug_on): print("DEBUG - DB Connection settings:", mysql_variables.db_host, mysql_variables.db_user, mysql_variables.db_pass, mysql_variables.db_name)     # DEBUG - Print DB info

try:
    db = MySQLdb.connect(host=mysql_variables.db_host, user=mysql_variables.db_user, password=mysql_variables.db_pass, database=mysql_variables.db_name)
    curs = db.cursor()
except MySQLdb.Error as er:
        print("ERROR - Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

#---------------------------------------------------------
# FUNCTION: Write to DB
# Table structure: CREATE TABLE buttondata(
#	id INT PRIMARY KEY auto_increment,
#	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
#	category TEXT,
#	state TEXT); 
#
#---------------------------------------------------------
def write_sql_to_file(file_name, sql, with_header=True, delimiter=',',quotechar='"',quoting=csv.QUOTE_NONNUMERIC, con_sscursor=False):
    
    if(debug_on): print("DEBUG - Write to file function")

    cur = db.cursor(pymysql.cursors.SSCursor) if con_sscursor else db.cursor()
    cur.execute(sql)
    header = [field[0] for field in cur.description]
    
    # Open function - https://docs.python.org/3/library/functions.html#open
    ofile = open(file_name,'ab')                 # Open file for Write (and create if needed) + Binary mode

    try:
        # CSV functions - https://docs.python.org/3/library/csv.html
        csv_writer = csv.writer(ofile, delimiter=delimiter, quotechar=quotechar,quoting=quoting)

        if with_header:
            csv_writer.writerow(header)
        if con_sscursor:
            while True:
                x = cur.fetchone()
                if x:
                    csv_writer.writerow(x)
                else:
                    break
        else:
            for x in cur.fetchall():
                csv_writer.writerow(x)
    finally:
        cur.close()     # close cursor
        ofile.close()   # close file

#---------------------------------------------------------

# Set filename, SQL query
file_name = "~/Baby-logger/" + "button-data_" + datetime.datetime.now().strftime("%Y-%m-%d") + ".csv";
sql = "SELECT * FROM buttondata ORDER BY id DESC"

if(debug_on): print("DEBUG - File:", file_name, "SQL function:", sql)

# Call write function
write_sql_to_file(file_name,sql)

time.sleep(1)

# Close DB
db.close()
