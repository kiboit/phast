.PHONY : all clean

all : browsers.json

test : browsers.json
	./test.php | parallel -j15 --bar --joblog joblog --resume-failed | tee test.out

clean :
	rm -f browsers.json
	rm -f joblog

browsers.json :
	curl -fSs -u "$$BROWSERSTACK_USERNAME:$$BROWSERSTACK_ACCESS_KEY" https://api.browserstack.com/automate/browsers.json > $@~
	mv $@~ $@
