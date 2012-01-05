#!/bin/bash


if (test $(whoami) != 'root') then
	echo "Please, run this script as root"
	exit
fi

echo -e "\033[31mStarting wolinit...\033[0m"

#cd into the repertory init
cd /etc/init.d/

echo -e "\033[31mGenerating automatic script...\033[0m"
#creating and writing the file for wake on lan
echo '#!/bin/bash' > wakeonlaninit
cmd1="ethtool -s"

#getting the interface's name
inter=`ifconfig | grep Ethernet | sed 's/^\([a-zA-Z0-9]*\).*/\1/'`
cmd2="wol g"
echo $cmd1" "$inter" "$cmd2 >> wakeonlaninit
echo 'exit' >> wakeonlaninit

#setting execution permissions
chmod a+x wakeonlaninit

echo -e "\033[31mExecuting the script...\033[0m"
#executing the script
./wakeonlaninit

echo -e "\033[31mAdding script to init programs...\033[0m"
#adding the script to init programs
update-rc.d -f wakeonlaninit defaults

#getting the mac address to print it to the user
mac=`ifconfig | grep HW | sed 's/^.*\(\([a-fA-F0-9]\{2\}[:]\)\{5\}[a-fA-F0-9]\{2\}\).*/\1/'`
ip=`ifconfig | grep Difus | sed 's/^.*[:][ ]\?\(\(25[0-4]\|2[0-4][0-9]\|1\?[0-9]\?[0-9]\.\)\{3\}\(1\?[0-9]\?[0-9]\|2[0-4][0-9]\|25[0-4]\)\)[ ].*/\1/'`

#creating and writing the file for turn off
echo '#!/bin/bash' > turnoffinit
echo 'countSeconds() {' >> turnoffinit
echo '    i=$1' >> turnoffinit
echo '    echo " "' >> turnoffinit
echo '    while [[ $i -ge 0 ]]' >> turnoffinit
echo '      do' >> turnoffinit
echo '        echo -e "\r "$i" \c"' >> turnoffinit
echo '        sleep 1' >> turnoffinit
echo '        i=$(expr $i - 1)' >> turnoffinit
echo '    done' >> turnoffinit
echo '    echo " -  "' >> turnoffinit
echo '}' >> turnoffinit
echo '' >> turnoffinit
echo 'while :' >> turnoffinit
echo 'do' >> turnoffinit
echo '  nc 127.0.0.1 6956 > /tmp/wolfilereceived.wol.out' >> turnoffinit
echo '  cat /tmp/wolfilereceived.wol.out | grep 'shutdown' >> /dev/null' >> turnoffinit
echo '  if [ $? -eq 0 ]; then' >> turnoffinit
echo '    n=60; ' >> turnoffinit
echo '    while ((n>=0));' >> turnoffinit
echo '    do' >> turnoffinit
echo '      sleep 1; ' >> turnoffinit
echo '      let c=(60-n); ' >> turnoffinit
echo '      echo $c; ' >> turnoffinit
echo '      echo "# Somebody requested shutdown of your computer. If you do not press Cancel, it will shutdown in $n seconds"; ' >> turnoffinit
echo '      let n--; ' >> turnoffinit
echo '    done | zenity --progress --title="Extinction?" --auto-close; ' >> turnoffinit
echo '    if [ "$?" == 0 ]; then ' >> turnoffinit
echo '      shutdown -h now' >> turnoffinit
echo '    fi;' >> turnoffinit
echo '  fi;' >> turnoffinit
echo 'done;' >> turnoffinit
echo '' >> turnoffinit
echo 'sed "s/^\(.*\)$/echo '\1' >> turnoffinit/"' >> turnoffinit

#executing the script
./wakeonlaninit

#setting execution permissions
chmod a+x turnoffinit

#adding the script to init programs
update-rc.d -f turnoffinit defaults

#Printing the final message
echo -e ""
echo -e "\033[31mThe script that enables wake on LAN for your computer will now run every time your computer starts !\033[0m"
echo -e "\033[31mIf the wake on LAN option is enabled in the BIOS, your computer will now be able to wake on LAN.\033[0m"
echo -e "\033[34mYour MAC address is : "$mac"\033[0m"
echo -e "\033[34mYour IP address is : "$ip"\033[0m"

exit
