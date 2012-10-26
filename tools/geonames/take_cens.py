#!/usr/bin/env python

import sys, os, time, json

if 3 != len(sys.argv):
    sys.stderr.write("use as " + sys.argv[0] + " infile.json outfile.js")
    sys.exit()

fnamein = sys.argv[1]
fnameout = sys.argv[2]

fhin = open(fnamein, "r")
jsonin = fhin.read()
fhin.close()

cinfo = json.loads(jsonin)

ccens = {}

fhout = open(fnameout, "w")
fhout.write("country_centers = {};\n")

for onec in cinfo['geonames']:
    cc = onec['countryCode']
    over_merid = False
    bBoxWest = onec['bBoxWest']
    bBoxEast = onec['bBoxEast']
    if bBoxWest > bBoxEast:
        bBoxEast += 360
        over_merid = True
    bBoxNorth = onec['bBoxNorth']
    bBoxSouth = onec['bBoxSouth']
    clon = (bBoxWest + bBoxEast) / 2.0
    if clon > 180:
        clon -= 180
    clat = (bBoxNorth + bBoxSouth) / 2.0
    ccens[cc] = {'lon': clon, 'lat': clat}

    fhout.write("country_centers['" + cc + "'] = {'lon': " + str(clon) + ", 'lat': " + str(clat) + "};\n")

fhout.close()


