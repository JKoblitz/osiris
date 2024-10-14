
# Setup optional jobs

All optional jobs need Python to be installed. Please look up [how to install](https://wiki.python.org/moin/BeginnersGuide/Download) the latest Python version on your OS. You will also need `pip` to install packages.

When you have Python and pip installed, you can **install the MongoDB-Connector** that is required for all jobs by running:

```bash
pip install pymongo
```

Additionally, you have to copy the **configuration file** `config.default.ini` in the jobs folder and rename it to `config.ini`. 
In this file, you must modify the values according to your needs. 


## Setup the queue job feature

The queue job gets new activities from online sources and saves them in a queue. Users will be informed when new activities are waiting in the queue and they can easily add them to OSIRIS.


### Prepare

To set up this feature, you must first install diophila with pip:

```bash
pip install nameparser levenshtein
```

Additionally, you must change the OpenAlex-ID of your institute in the `config.ini` file.

```ini
[OpenAlex]
Institution = I7935750
```

### Init Cron Job

Finally, we init a cron job on the device. We use the editor nano for this (default on most devices is vi). The following settings are used to run the job weekly (2 a.m. on Sunday).

```bash
EDITOR=nano crontab -e 

# enter this as cronjob:
0 2 * * 0 python3 /var/www/html/jobs/openalex_parser.py

# press Ctrl+O to save and Ctrl+X to exit
```
