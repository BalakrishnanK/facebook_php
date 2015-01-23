#! /usr/bin/env python                                                                                                                         



with open('e63.txt') as fp:
    for line in fp:
        s = line.split(",")
#        s = line.replace("\n\n",",")
        print len(s)
