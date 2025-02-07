### SSH generate 
  
  server:  ssh-keygen -t rsa -b 4096 -f /root/id_ua_shop
  Local : chmod 600 ~/.ssh/id_ua_shop
  
  server: cat ~/.ssh/id_ua_shop.pub >> ~/.ssh/authorized_keys

server: nano /etc/ssh/sshd_config
  PasswordAuthentication no
  PubkeyAuthentication yes

  ### SSH encryption and decryption
  
    # encrypted
    openssl aes-256-cbc -a -pbkdf2 -salt  -in secret.key -out secret.enc
  
    # decrypt
    openssl aes-256-cbc -a -pbkdf2 -d -in secret.enc -out decrypt.key
