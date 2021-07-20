---
layout: post
title: Publishing an event stream
tags: [event, stream, event-sourcing]
draft: true
---

With [Qandidate.com] we use EventSourcing for our applications and recently we also started using micro-services (using Docker).
One of the challenges with that approach, is exposing (some of) the events to other components that act on them.

We wanted to keep all the components self-contained and unaware of other components as much as possible. This means
that we need to expose the events in some way that other components can read and process those events, without those components
needing to know where all the event streams can be found.

The idea was, that each component could just say "I'm interested in events X and Y" and that the component then got a pointer to 
the feeds that expose that event. That way you can spin up new components that expose that event, without the need to tell the
interested component that he needs to poll a new location.

So we need a few things to make this work:

- A simple way to read/write atom feeds
- Some sort of service discovery, where components would register which events they expose where.

### Service Discovery

One of the things we looked at was [Consul], a service discovery tool written in Go.

[Qandidate.com]: http://qandidate.com
[Consul]: https://github.com/hashicorp/consul
