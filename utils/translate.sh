pwd=`pwd`
tdir=$pwd/../tests/src
pname='project'
cd $tdir
xgettext -D ./ --add-comments \
--add-location  \
--from-code=utf-8 \
--no-wrap \
--copyright-holder="My Copyright Message" \
--package-name="My Package Name" \
--package-version="V1.8.5" \
--msgid-bugs-address="myemil@mail.com" \
-o messages.pot

cd pwd
for i in 'en ja ko'
do
    php point.phar -u -v -s zh -d jp -f $tdir/locale/ja/LC_MESSAGES/$pname-ja.po -o $tdir/locale/ja/LC_MESSAGES/$pname-ja.po
done