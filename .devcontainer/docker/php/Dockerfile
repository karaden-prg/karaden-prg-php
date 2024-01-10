FROM mcr.microsoft.com/devcontainers/php:7.4

ARG USERNAME=${USERNAME}
ARG USER_UID=${USER_UID}
ARG USER_GID=${USER_GID}
ARG TIME_ZONE=${TIME_ZONE}
ARG LOCALE=${LOCALE}

ENV LANG=${LOCALE}
ENV LANGUAGE=${LANGUAGE}
ENV TZ=${TIME_ZONE}

RUN curl -fsSL https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --yes --dearmor -o /usr/share/keyrings/yarnkey.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/yarnkey.gpg] https://dl.yarnpkg.com/debian stable main" > /etc/apt/sources.list.d/yarn.list

RUN apt-get update \
  && apt-get install -y locales \
  && locale-gen ja_JP.UTF-8 \
  && localedef -f UTF-8 -i ja_JP ja_JP.UTF-8 \
  && update-locale LANG=ja_JP.UTF-8

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

RUN ln -snf /usr/share/zoneinfo/${TIME_ZONE} /etc/localtime \
  && echo ${TIME_ZONE} > /etc/timezone 

USER ${USER_UID}

RUN curl https://get.volta.sh | bash \
  && echo 'export VOLTA_HOME="$HOME/.volta"' >> ~/.bash_profile \
  && echo 'export PATH="$VOLTA_HOME/bin:$PATH"' >> ~/.bash_profile

RUN echo -n "\n\
LANG="${LANG}"\n\
LANGUAGE="${LANGUAGE}"\n\
LC_ALL="${LOCALE}"\n\
" >> ~/.bashrc
