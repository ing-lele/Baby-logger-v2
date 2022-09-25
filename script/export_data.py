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
import time
import datetime
import pymysql
pymysql.install_as_MySQLdb()
import MySQLdb

#! /usr/bin/python3
current_path = "/home/pi/Baby-logger/script/"
sys.path.insert(0, current_path)    # Add script folder to default import search

import mysql_variables  #Import MySQL variable

# DEBUG - Enable debug print
debug_on = 0

#----------------------------------------------------------
# MySQL variable are defined in mysql_variables.py module
# MySQLdb.db_host 
# MySQLdb.db_user 
# MySQLdb.db_pass 
# MySQLdb.db_name 

#----------------------------------------------------------
# FUNCTION: Read table from DB
#
# Table structure: buttondata(
#	id INT PRIMARY,
#	created TIMESTAMP,
#	category TEXT,
#	state TEXT); 
#
#---------------------------------------------------------
def fetch_table_data(table_name):
    
    if(debug_on): print("DEBUG - Query SQL function")

    #Connect to DB
    try:
        db = MySQLdb.connect(host=mysql_variables.db_host, user=mysql_variables.db_user, password=mysql_variables.db_pass, database=mysql_variables.db_name)
        curs = db.cursor()
    except MySQLdb.Error as er:
        print("ERROR - Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

    try:
        # Set and execute query
        sql = "SELECT * FROM " + table_name + " ORDER BY id DESC"
        if(debug_on): print("DEBUG -", sql)
        curs.execute(sql)

        # Get header
        header = [row[0] for row in curs.description]

        # Get data
        rows = curs.fetchall()
    finally:
        # Closing connection
        db.close()

    return header, rows
    
#---------------------------------------------------------
# FUNCTION: Write table data to CSV file
#
#---------------------------------------------------------
def export_file(table_name, file_name):
    
    if(debug_on): print("DEBUG - Write to file function")
    
    # Read SQL data
    header, rows = fetch_table_data(table_name)

    try:
        # Create CSV file
        ofile = open(file_name,'w')
       
        # Write header
        ofile.write(','.join(header) + '\n')

        # Write rows
        for row in rows:
            ofile.write(','.join(str(r) for r in row) + '\n')

    finally:
        # Close file
        ofile.close()

    print("LOG - " + str(len(rows)) + " rows written successfully to " + ofile.name)

#---------------------------------------------------------