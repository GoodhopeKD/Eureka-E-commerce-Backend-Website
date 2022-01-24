import React from "react";
import styled from "styled-components";
import LeftImage from "../Assets/images/featuresImageLeft.png";
import RightImage from "../Assets/images/featuresImageRight.png";
import CenterImage from "../Assets/images/featuresImageCenter.png";
import { colors } from "../config/colors";

const Features = () => {
  return (
    <Container>
      <Heading>You Can</Heading>
      <Content>
        <Right>
          <div>
            <Header>
              Inside, <br /> who's that ?
            </Header>
            <Discription>
              2020 was alot
              <br />
              <br />
              Events are important for us all
              <br />
              <br />
              Choose any event(s) suited for you with the venue and ticket
              detailed
            </Discription>
          </div>
          <Image src={RightImage} />
        </Right>
        <Center>
          <div>
            <Header>Earn monry from selling your stuff</Header>
            <Discription>
              Leaving the country or got stuff to sell/don't need and want extra
              cash
              <br />
              <br />
              Upload items with details
              <br />
              <br />
              we verify and ship
            </Discription>
          </div>
          <Image src={CenterImage} />
        </Center>
        <Left>
          <div>
            <Header>Find location based products</Header>
            <Discription>
              Vendors and distributed across the cities
              <br />
              <br />
              Discover products around you and in specific cities shop and get
              your items
              <br />
              <br />
            </Discription>
          </div>
          <Image src={LeftImage} />
        </Left>
      </Content>
    </Container>
  );
};

export default Features;

const Container = styled.div`
  height: 1100px;
  width: 100%;
  background-color: ${colors.black};
  display: flex;
  justify-content: center;
  align-items: center;
  overflow-x: auto;
  overflow-y: hidden;

  ::-webkit-scrollbar {
    width: 0;
  }

  @media screen and (max-width: 760px) {
    height: 500px;
    justify-content: flex-start;
    align-items: flex-start;
  }
`;

const Heading = styled.h2`
  display: none;

  @media screen and (max-width: 760px) {
    display: block;
    position: absolute;
    color: white;
    font-size: 2rem;
    padding: 2rem;
  }
`;

const Content = styled.div`
  height: 80%;
  width: 70%;
  display: flex;
  background-color: ${colors.black};
  justify-content: center;
  align-items: center;
  color: white;
  margin-top: 3rem;

 

  @media screen and (max-width: 760px) {
    height: 90%;
    min-width: 240%;
    margin-top: 5rem;
  }
`;

const Right = styled.div`
  height: 100%;
  width: 30%;
  margin-top: 20rem;
  padding: 1rem;

  @media screen and (max-width: 760px) {
    display: flex;
    flex-direction: row-reverse;
    justify-content: space-around;
    align-items: center;
    margin-top: 0rem;
    height: 85%;
    width: 30%;
    margin-right: 3rem;
    padding: 0rem;
  }
`;

const Header = styled.h2`
  font-size: 2.5rem;
  font-weight: bold;
  line-height: 2.5rem;
  margin-top: 0;
  color: ${colors.primary};

  @media screen and (max-width: 760px) {
    font-size: 2rem;
    font-weight: bold;
    line-height: 2rem;
  }
`;

const Discription = styled.span`
  font-size: 1.1rem;
`;

const Center = styled.div`
  height: 100%;
  width: 33%;
  padding: 1rem;

  @media screen and (max-width: 760px) {
    display: flex;
    flex-direction: row-reverse;
    justify-content: space-around;
    align-items: center;
    margin-top: 0rem;
    height: 85%;
    width: 30%;
    margin-right: 3rem;
    padding: 0rem;
  }
`;

const Left = styled.div`
  height: 100%;
  width: 30%;
  margin-top: 20rem;
  padding: 1rem;

  @media screen and (max-width: 760px) {
    display: flex;
    flex-direction: row-reverse;
    justify-content: space-around;
    align-items: center;
    margin-top: 0rem;
    height: 85%;
    width: 30%;
    padding: 0rem;
  }
`;

const Image = styled.img`
  height: 100%;
  width: 100%;
  margin-top: 0rem;

  @media screen and (max-width: 760px) {
    width: 40%;
    height: 85%;
  }
`;
