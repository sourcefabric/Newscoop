Appendix D - The INI Format
===========================

This appendix describes the INI format used to describe parameters.

The INI format is only able to describe parameters. You cannot define imports,
nor can you import other resources from an INI file.

As for the XML and YAML format, the INI format supports placeholders.

Parameters
----------

Only the `[parameters]` section of an INI file is parsed:

    [ini]
    [parameters]
      foo = bar
      bar = %foo%

The file can only define simple key/value pairs. The same parsing rules as the
ones for the PHP built-in `parse_ini_file()` function apply.
