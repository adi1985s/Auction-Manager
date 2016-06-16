#-*-coding: utf-8-*-
#!/usr/bin/python
import sys, os, os.path, time

# typy plików do sprawdzenie
exts = ('cpp', 'h', 'py', 'php', 'phtml', 'css')

t1 = time.time()

# Definiowanie własnych rozszerzeń
if sys.argv.count('--extensions') and sys.argv[1] == '--extensions':
	exts += tuple(sys.argv[2:])

lines = 0
files = 0
for root, dirnames, filenames in os.walk(os.path.dirname(os.path.abspath(__file__))):
	for filename in filenames:
		if filename.endswith(exts) and filename != sys.argv[0]:
			lines += len(open('/'.join([root, filename])).readlines());
			files += 1

print 'Linii kodu:', lines
print 'Łącznie plików:', files
print 'Czas sprawdzenia:', ''.join([str(time.time() - t1)[:6], 's'])
