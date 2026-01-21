FROM php:7.4.33-cli
COPY --from=composer /usr/bin/composer /usr/bin/composer

ARG USERNAME
ARG USER_UID
ARG USER_GID
ARG TIME_ZONE
ARG LOCALE
ARG WORKING_DIR

ENV LANG=${LOCALE}
ENV TZ=${TIME_ZONE}

RUN apt-get update \
  && apt-get install -y sudo git

RUN apt-get install -y libzip-dev \
  && docker-php-ext-install zip
RUN pecl install xdebug-3.1.6 \
  && docker-php-ext-enable xdebug

RUN apt-get install -y locales \
  && locale-gen ja_JP.UTF-8 \
  && localedef -f UTF-8 -i ja_JP ja_JP.UTF-8 \
  && update-locale LANG=ja_JP.UTF-8

RUN ln -snf /usr/share/zoneinfo/${TIME_ZONE} /etc/localtime \
  && echo ${TIME_ZONE} > /etc/timezone 

RUN /usr/sbin/groupadd --gid ${USER_GID} ${USERNAME} \
  && /usr/sbin/useradd -s /bin/bash --uid ${USER_UID} --gid ${USER_GID} -m ${USERNAME} \
  && echo ${USERNAME} ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/${USERNAME} \
  && chmod 0440 /etc/sudoers.d/${USERNAME}

USER ${USER_UID}

RUN curl https://get.volta.sh | bash

RUN /bin/echo -e "\n\
LANG=\"${LANG}\"\n\
LC_ALL=\"${LOCALE}\"\n\
" >> ~/.bashrc

RUN sudo rm -rf /var/task \
  && sudo ln -s ${WORKING_DIR} /var/task
