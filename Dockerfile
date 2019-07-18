FROM ubuntu:trusty
MAINTAINER Simon Dittlmann <simon.dittlmann@tado.com>, Inaki Anduaga <inaki.anduaga@tado.com>

# ===============================================================================
# BASE UBUNTU + HHVM Installation
#
# Code Taken from brunoric/hhvm:deb ###
# ===============================================================================

#
# Flag commands as non-interactive
#
ENV DEBIAN_FRONTEND=noninteractive

#
# Bootstrap & general tools
#
RUN apt-get update && apt-get upgrade -y
RUN apt-get update && apt-get install -y wget curl git

#
# Installing packages
#
RUN apt-get update && \
  apt-get install -y software-properties-common apt-transport-https && \
  apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xB4112585D386EB94 && \
  add-apt-repository "deb http://dl.hhvm.com/ubuntu $(lsb_release -sc)-lts-3.24 main" && \
  apt-get update && \
  apt-get install -y hhvm

RUN curl -sS https://getcomposer.org/download/1.6.5/composer.phar -o composer.phar && \
   mv composer.phar /usr/local/bin/composer && \
   chmod +x /usr/local/bin/composer


RUN apt-get clean && apt-get autoremove -y

#
# Make php cli use hhvm instead of php (so we can use composer, phpunit etc)
#
RUN sudo /usr/bin/update-alternatives --install /usr/bin/php php /usr/bin/hhvm 60
