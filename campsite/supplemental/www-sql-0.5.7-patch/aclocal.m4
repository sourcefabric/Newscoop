dnl   JH_FIND_LIB(lib, func, args, if-found, if-not-found, other-libs)
dnl attempt to find a library (lib), containing a function (func), using a
dnl number of different C compiler link options (args). and perform an action.
dnl in the if-found action, the variable arg will be available which contains
dnl the argument which matched
dnl
dnl eg JH_FIND_LIB(mysqlclient, mysql_connect, ["" "-L/usr/local/mysql/lib"],
dnl      MYSQL_LIB=$jh_cv_lib_mysqlclient_mysql_connect,
dnl      [AC_MSG_ERROR([Library not found.])], [-lm])

AC_DEFUN(JH_FIND_LIB,
[AC_MSG_CHECKING([location of $1 library])
jh_lib_var=`echo $1['_']$2 | sed 'y%./+-%__p_%'`
AC_CACHE_VAL(jh_cv_lib_$jh_lib_var, [
eval "jh_cv_lib_$jh_lib_var=no"
jh_saved_LDFLAGS="$LDFLAGS"
jh_saved_LIBS="$LIBS"
for arg in $3; do
  LDFLAGS="$jh_saved_LDFLAGS $arg"
  LIBS="-l$1 $6 $jh_saved_LIBS"
  AC_TRY_LINK(dnl
  ifelse([$2], [main], ,dnl
[/* Override any gcc2 internal prototype to avoid an error.  */
]ifelse(AC_LANG, CPLUSPLUS, [#ifdef __cplusplus
extern "C"
#endif
])dnl
[/* We use char because int might match the return type of a gcc2
    builtin and then its argument prototype would still apply.  */
char $2();
]),
    [$2()],
    [eval "jh_cv_lib_$jh_lib_var=$arg"; break], [:])
done
LDFLAGS="$jh_saved_LDFLAGS"
LIBS="$jh_saved_LIBS"
])
jh_val=`eval "echo \`echo '$jh_cv_lib_'$jh_lib_var\`"`
AC_MSG_RESULT([$jh_val])
if test "$jh_val" = 'no'; then
  ifelse([$5], [], :, [$5])
else
  ifelse([$4], [], :, [$4])
fi])


dnl   JH_FIND_HEADER(defs, args, if-found, if-not-found)
dnl Similar to previous macro, but for finding a header file.
dnl
dnl eg JH_FIND_HEADER([mysql/mysql.h], ["" "-I/usr/local/mysql/include"],
dnl      MYSQL_INC=$jh_cv_header_mysql_mysql_h,
dnl      [AC_MSG_ERROR([Header not found.])])

AC_DEFUN(JH_FIND_HEADER,
[AC_MSG_CHECKING([location of $1 header])
jh_safe=`echo "$1" | sed 'y%./+-%__p_%'`
AC_CACHE_VAL(jh_cv_header_$jh_safe, [
eval "jh_cv_header_$jh_safe=no"
jh_saved_CPPFLAGS=$CPPFLAGS
for arg in $2; do
  CPPFLAGS="$jh_saved_CPPFLAGS $arg"
  AC_TRY_CPP([#include <$1>], [eval "jh_cv_header_$jh_safe=$arg"; break])
done
CPPFLAGS=$jh_saved_CPPFLAGS])
jh_val=`eval "echo \`echo '$jh_cv_header_'$jh_safe\`"`
AC_MSG_RESULT([$jh_val])
if test "$jh_val" = "no"; then
  ifelse([$4], [], :, [$4])
else
  ifelse([$3], [], :, [$3])
fi])


dnl   JH_FIND_DIR(dirs, var)
dnl assign the first directory in the list dirs to the variable var.

AC_DEFUN(JH_FIND_DIR,
[for dir in $1; do
  if test -d $dir; then
    $2=$dir
    break
  fi
done])

