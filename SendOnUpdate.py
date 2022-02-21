from time import time


class SendOnUpdate(dict):
    def __setitem__(self, key, value):
        if self.get(key) != value:
            print(f"[+] i will send the request now for {key} => {value}")
            super().__setitem__(key, value)
        else:
            super().__setitem__(key, None)
            
            
if __name__ == "__main__":

def test():
  dict1 = SendOnUpdate()
  dict1['username'] = None
  start_time = time()
  counter = 0
  end_time = int(time() - start_time)
  while end_time < 30:
      for i in range(10):
          dict1['username'] = f"username {i}"
      end_time = int(time() - start_time)
      counter += 1

  print(f"the total number of requests send: {counter}")
