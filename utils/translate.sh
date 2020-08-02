
for i in zh_TW zh_HK
do
    shee="php ../src/cmd.php -v -s zh -d $i -f ${PWD}/../en.po -o ${PWD}/project-$i.po"
    echo $shee
    eval $shee
done