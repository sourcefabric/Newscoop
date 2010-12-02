#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, os, time

def log_err(msg):
    try:
        sys.stderr.write(msg)
        if not msg.endswith("\n"):
            sys.stderr.write("\n")
        sys.stderr.flush()
    except Exception, e:
        pass

try:
    import MySQLdb as db
except Exception, e:
    log_err("can not load mysql db interface: " + str(e))
    sys.exit(1)

try:
    from geocities_dsn import cs_dsn
except Exception, e:
    log_err("can not import mysql dsn specification: " + str(e))
    sys.exit(1)

try:
    from geocities_src import geo_file_name
except Exception, e:
    log_err("can not import geo names specification: " + str(e))
    sys.exit(1)


CITIESFILE = geo_file_name
LINECOLUMNS = 19

LCOL_ID, LCOL_MAIN, LCOL_ASCII, LCOL_OTHER, LCOL_LAT, LCOL_LON, LCOL_FCLASS, LCOL_FCODE = list(range(8))
LCOL_CC, LCOL_CCOTHER, LCOL_A1C, LCOL_A2C, LCOL_A3C, LCOL_A4C  = list(range(8, (8 + 6)))
LCOL_POP, LCOL_ELE, LCOL_ELEAVG, LCOL_TZONE = list(range(14, (14 + 4)))
LCOL_MOD = 18

CITY_COLUMNS = ['city_id', 'city_type', 'population', 'latitude', 'longitude', 'elevation', 'country_code', 'time_zone', 'modified']
CITY_COLUMNS_STR = ", ".join(CITY_COLUMNS)
CITY_QM = ['%%s'] * len(CITY_COLUMNS)
CITY_QM_STR = ", ".join(CITY_QM)

NAME_COLUMNS = ['city_id', 'city_name', 'name_type']
NAME_COLUMNS_STR = ", ".join(NAME_COLUMNS)
NAME_QM = ['%s'] * len(NAME_COLUMNS)
NAME_QM_STR = ", ".join(NAME_QM)

CITY_INSERT = "INSERT INTO CityLocations (position, %s) VALUES (%%s, %s)" % (CITY_COLUMNS_STR, CITY_QM_STR)
NAME_INSERT = "INSERT INTO CityNames (%s) VALUES (%s)" % (NAME_COLUMNS_STR, NAME_QM_STR)
#print(CITY_INSERT)
#print(NAME_INSERT)

class GeoNamesLoader(object):
    dbconn = None
    dbcurs = None

    def db_disconnect(self):
        try:
            self.dbcurs.close()
            self.dbconn.close()
        except Exception, e:
            pass

        self.dbcurs = None
        self.dbconn = None

    def db_connect(self, dsn_data):
        if self.dbconn:
            self.db_disconnect()

        try:
            self.dbconn = db.connect(host = dsn_data["host"], user = dsn_data["user"], passwd = dsn_data["password"], db = dsn_data["dbname"])
            self.dbconn.autocommit(False)
            self.dbcurs = self.dbconn.cursor()
            self.dbcurs.execute("SET AUTOCOMMIT=0")
            self.dbcurs.execute("SET NAMES 'utf8'")
        except Exception, e:
            log_err("can not connect to the database: " + str(e))
            return False

        return True

    def db_commit(self):
        if not self.dbconn:
            log_err("not connected to the database")
            return False
        try:
            self.dbconn.commit()
            self.dbcurs.close()
            self.dbcurs = self.dbconn.cursor()
            self.dbcurs.execute("SET AUTOCOMMIT=0")
            self.dbcurs.execute("SET NAMES 'utf8'")
        except Exception, e:
            log_err("can not commit changes at the database: " + str(e))
            return False

        return True

    def insert_name(self, ins_data):
        if not self.dbconn:
            log_err("not connected to the database")
            return False

        try:
            self.dbcurs.execute(NAME_INSERT, ins_data)

        except Exception, e:
            log_err("can not insert city name into the database: " + str(e))
            return False

        return True

    def insert_city(self, ins_data, position):
        if not self.dbconn:
            log_err("not connected to the database")
            return False

        try:
            city_insert_str = CITY_INSERT % (position,)
            #print(city_insert_str)
            #print(ins_data)
            self.dbcurs.execute(city_insert_str, ins_data)

        except Exception, e:
            log_err("can not insert city position/info into the database: " + str(e))
            return False

        return True

    def load_source(self, filename):
        city_ids = []

        try:
            infile = open(filename)
        except Exception, e:
            log_err("can not open cities file: " + str(e))
            return False

        for line in infile.readlines():
            if 0 == len(line):
                continue
            line_list = line.split("\t")
            if not LINECOLUMNS == len(line_list):
                log_err("wrong number of line columns: " + str(len(line_list)) + " instead of " + str(LINECOLUMNS) + ":\n" + line)
                continue
            if line_list[0].startswith("#"):
                continue

            city_id = str(line_list[LCOL_ID])
            if city_id in city_ids:
                log_err("city id already in used ids: " + str(city_id))
                continue
            city_ids.append(city_id)

            city_name_main = line_list[LCOL_MAIN]
            city_name_ascii = line_list[LCOL_ASCII]
            city_name_lower = city_name_ascii.lower()
            city_name_other = line_list[LCOL_OTHER].split(",")

            city_type = line_list[LCOL_FCODE]

            city_lat = line_list[LCOL_LAT]
            city_lon = line_list[LCOL_LON]
            city_ele = line_list[LCOL_ELE]
            if 0 == len(city_ele):
                city_ele = line_list[LCOL_ELEAVG]
            if 0 == len(city_ele):
                city_ele = None

            city_pop = line_list[LCOL_POP]
            city_cc = line_list[LCOL_CC]
            city_tz = line_list[LCOL_TZONE]

            city_pos = "PointFromText('POINT(" + str(city_lat) + " " + str(city_lon) + ")')"

            city_mod = line_list[LCOL_MOD]

            city_data = [city_id, city_type, city_pop, city_lat, city_lon, city_ele, city_cc, city_tz, city_mod]
            res = self.insert_city(city_data, city_pos)
            if not res:
                return -1

            used_names = []
            #for one_name, one_type in [[city_name_main, "main"], [city_name_ascii, "ascii"], [city_name_lower, "lower"]]:
            for one_name, one_type in [[city_name_main, "main"], [city_name_ascii, "ascii"]]:
                if one_name in used_names:
                    continue
                used_names.append(one_name);
                res = self.insert_name([city_id, one_name, one_type])
                if not res:
                    return -1

            for one_name in city_name_other:
                if one_name in used_names:
                    continue
                used_names.append(one_name);
                res = self.insert_name([city_id, one_name, "other"])
                if not res:
                    return -1

        self.db_commit()
        infile.close()

if ('__main__' == __name__):
    loader = GeoNamesLoader()

    loader.db_connect(cs_dsn)
    loader.load_source(geo_file_name)
    loader.db_disconnect()


