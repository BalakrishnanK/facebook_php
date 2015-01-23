#! /usr/bin/env python                                                                                                                         



with open('emailid_1.txt') as fp:
    for line in fp:
        try:
            if line.split('\"',4)[2] == ',':
                print line.split('\"',4)[3].replace('\n','');
            elif line.split('\"',4)[2] == '\n':
                i=0
        #            print line.split('\"',2)[0].replace('\n','')+" nothing";
            else:
                print line.split('\"',4)[2].replace('\n','');
        except(TypeError,IndexError):
            i1=0
