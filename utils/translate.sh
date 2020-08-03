
for i in zh_TW zh_HK
do
    shee="php ../src/cmd.php -v -s zh -d $i -f ${PWD}/../tests/Localizable.strings -o ${PWD}/$i.Localizable.strings"
    echo $shee
    eval $shee
done