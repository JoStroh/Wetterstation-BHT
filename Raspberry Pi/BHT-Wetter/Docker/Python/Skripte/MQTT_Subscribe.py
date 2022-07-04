#Quelle: https://github.com/eclipse/paho.mqtt.python/issues/655

import socket
import threading
from paho.mqtt.client import Client

def subscribe(
    topic: str ,
    hostname: str,
    timeout: float,
    username: str,
    password: str,
    **mqtt_kwargs,
):
    """
    Modeled closely after the paho version, this also includes some try/excepts and
    a timeout. Note that this _does_ disconnect after receiving a single message.
    """


    lock: Optional[threading.Lock]

    def on_connect(client, userdata, flags, rc):
        client.subscribe(userdata["topic"])
        return

    def on_message(client, userdata, message):

        userdata["messages"] = message
        client.disconnect()

        if userdata["lock"]:
            userdata["lock"].release()

        return

    if timeout:
        lock = threading.Lock()
    else:
        lock = None

    topic = [topic] if isinstance(topic, str) else topic
    userdata: dict[str, Any] = {
        "topic": [(topic, mqtt_kwargs.pop("qos", 0)) for topic in topic],
        "messages": None,
        "lock": lock,
    }

    client = Client(userdata=userdata)
    client.username_pw_set(username, password)
    client.on_connect = on_connect
    client.on_message = on_message
    client.connect(hostname)

    if timeout is None:
        client.loop_forever()
    else:
        assert lock is not None
        lock.acquire()
        client.loop_start()
        lock.acquire(timeout=timeout)
        client.loop_stop()
        client.disconnect()

    return userdata["messages"]