import sys
import os
from sys import stdout

from alloc import alloc

class browse(alloc):

  one_line_help = "Provide web browser access to particular entities."

  # Setup the options that this cli can accept
  ops = []
  ops.append((''  ,'help           ','Show this help.'))
  ops.append(('q' ,'quiet          ','Run with no output except errors.'))
  ops.append(('p:','project=ID|NAME','A project ID, or a fuzzy match for a project name.'))
  ops.append(('t:','task=ID|NAME   ','A task ID, or a fuzzy match for a task name.'))
  ops.append(('c:','client=ID|NAME ','A client ID, or a fuzzy match for a client\'s name.'))
  ops.append(('i:','time=ID        ','A time sheet ID.'))

  # Specify some header and footer text for the help text
  help_text = 'Usage: %s [OPTIONS] ID|NAME\n'
  help_text+= one_line_help
  help_text+= '''\n\n%s

This program allows you to quickly jump to a particular alloc web page. It fires up 
$BROWSER on the location, or if the output is not a TTY it captures the output instead.
  
Examples:
alloc browse --task 123
alloc browse --task 123 > task123.html
alloc browse --project 1234
alloc browse --client 43432
alloc browse --time 213'''


  def run(self):

    # Get the command line arguments into a dictionary
    o, remainder = self.get_args(self.ops, self.help_text)

    # Got this far, then authenticate
    self.authenticate();


    self.quiet = o['quiet']
    projectID = 0
    taskID = 0
    clientID = 0

    # Get a projectID either passed via command line, or figured out from a project name
    if self.is_num(o['project']):
      projectID = o['project']
    elif o['project']:
      projectID = self.search_for_project(o['project'])

    # Get a taskID either passed via command line, or figured out from a task name
    if self.is_num(o['task']):
      taskID = o['task']
    elif o['task']:
      tops = {}
      tops["taskName"] = o["task"]
      tops["taskView"] = "prioritised"
      taskID = self.search_for_task(tops)

    # Get a clientID either passed via command line, or figured out from a client name
    if self.is_num(o['client']):
      clientID = o['client']
    elif o['client']:
      clientID = self.search_for_client({"clientName":o['client']})

    # url to alloc
    base = "/".join(self.url.split("/")[:-2])

    if taskID:
      url = base+"/task/task.php?sessID="+self.sessID+"&taskID="+taskID
    elif projectID:
      url = base+"/project/project.php?sessID="+self.sessID+"&projectID="+projectID
    elif clientID:
      url = base+"/client/client.php?sessID="+self.sessID+"&clientID="+clientID
    elif o['time']:
      url = base+"/time/timeSheet.php?sessID="+self.sessID+"&timeSheetID="+o['time']
    else: 
      self.die('Specify one of -t, -p, -c, etc.')


    # If we're redirecting stdout eg -t 123 >task123.html
    if not stdout.isatty():
      print self.get_alloc_html(url)

    elif url:
      if not 'BROWSER' in os.environ or not os.environ['BROWSER']:
        self.die('The environment variable $BROWSER has not been defined.')
      elif url:
        command = os.environ['BROWSER']+' "'+url+'"'
        if o['quiet']: command+=' >/dev/null'
        self.msg('Running: '+command)
        os.system(command)
